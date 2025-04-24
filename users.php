<?php

// GET action=list
// POST action=save, action=delete
header('Content-Type: application/json');
require 'db.php';
$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    $sql = 'SELECT username, role FROM users';
    $res = $conn->query($sql);
    $users = [];
    while ($row = $res->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode(['users' => $users]);
    exit;
}

if ($action === 'save') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $isEdit = $_POST['isEdit'] ?? '';

    // Insert or update
    if ($isEdit) {
        // Update existing user
        if ($password !== '') {
            $sql = 'UPDATE users SET password = ?, role = ? WHERE username = ?';
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sss', $password, $role, $isEdit);
        } else {
            $sql = 'UPDATE users SET role = ? WHERE username = ?';
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $role, $isEdit);
        }
    } else {
        // Create new user
        $sql = 'INSERT INTO users (username, password, role) VALUES (?, ?, ?)';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $username, $password, $role);
    }
    $stmt->execute();
    echo json_encode(['success' => true]);
    $stmt->close();
    exit;
}

if ($action === 'delete') {
    $username = $_POST['username'] ?? '';
    $sql = 'DELETE FROM users WHERE username = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    echo json_encode(['success' => true]);
    $stmt->close();
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid action']);
?>
