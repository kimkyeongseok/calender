<?
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $host = 'localhost';
        $db   = 'scheduler';
        $user = 'root';
        $pass = '1234';
        $this->conn = new mysqli($host, $user, $pass, $db);
        if ($this->conn->connect_errno) {
            throw new Exception('DB 연결 오류: ' . $this->conn->connect_error);
        }
        $this->conn->set_charset('utf8mb4');
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}

class UserManager {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function authenticate($username, $password) {
        $sql = 'SELECT username, role FROM users WHERE username=? AND password=?';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ss', $username, $password);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc();
    }

    public function listUsers() {
        $res = $this->conn->query('SELECT username, role FROM users');
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function saveUser($username, $password, $role, $isEdit = null) {
        if ($isEdit) {
            if ($password) {
                $sql = 'UPDATE users SET password=?, role=? WHERE username=?';
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param('sss', $password, $role, $isEdit);
            } else {
                $sql = 'UPDATE users SET role=? WHERE username=?';
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param('ss', $role, $isEdit);
            }
        } else {
            $sql = 'INSERT INTO users (username,password,role) VALUES (?,?,?)';
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('sss', $username, $password, $role);
        }
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function deleteUser($username) {
        $stmt = $this->conn->prepare('DELETE FROM users WHERE username=?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
}


class EventManager {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function listEvents($page = 1, $pageSize = 10) {
        $offset = ($page - 1) * $pageSize;
        // Total count
        $totalRes = $this->conn->query('SELECT COUNT(*) AS cnt FROM events');
        $total = (int)$totalRes->fetch_assoc()['cnt'];
        // Fetch events
        $stmt = $this->conn->prepare('SELECT * FROM events ORDER BY start DESC LIMIT ?, ?');
        $stmt->bind_param('ii', $offset, $pageSize);
        $stmt->execute();
        $events = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return ['total' => $total, 'events' => $events];
    }

    public function getEvent($id) {
        $stmt = $this->conn->prepare('SELECT * FROM events WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function saveEvent($evt, $owner) {
        if (!empty($evt['id'])) {
            $sql = 'UPDATE events SET title=?, start=?, end=?, type=?, location=?, participants=? WHERE id=?';
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('ssssssi', $evt['title'], $evt['start'], $evt['end'], $evt['type'], $evt['location'], $evt['participants'], $evt['id']);
        } else {
            $sql = 'INSERT INTO events (title,start,end,type,location,participants,owner) VALUES (?,?,?,?,?,?,?)';
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('sssssss', $evt['title'], $evt['start'], $evt['end'], $evt['type'], $evt['location'], $evt['participants'], $owner);
        }
        $stmt->execute();
        return ['success' => true, 'id' => $stmt->insert_id];
    }

    public function deleteEvent($id) {
        $stmt = $this->conn->prepare('DELETE FROM events WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function copyEvent($id) {
        $src = $this->getEvent($id);
        if (!$src) return false;
        unset($src['id']);
        return $this->saveEvent($src, $src['owner']);
    }
}


class AdminStats {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function getStats() {
        $total = (int)$this->conn->query('SELECT COUNT(*) AS cnt FROM events')->fetch_assoc()['cnt'];
        $byUser = $this->conn->query('SELECT owner AS user, COUNT(*) AS count FROM events GROUP BY owner')->fetch_all(MYSQLI_ASSOC);
        $events = $this->conn->query('SELECT * FROM events')->fetch_all(MYSQLI_ASSOC);
        return ['total' => $total, 'byUser' => $byUser, 'events' => $events];
    }
}
?>