<?php
// GET action=stats
header('Content-Type: application/json');
require 'db.php';
$action = $_GET['action'] ?? '';
if ($action === 'stats') {
    $totalRes = $conn->query('SELECT COUNT(*) AS cnt FROM events')->fetch_assoc();
    $total = (int)$totalRes['cnt'];
    $byUser = [];
    $res = $conn->query('SELECT owner AS user, COUNT(*) AS count FROM events GROUP BY owner');
    while ($row = $res->fetch_assoc()) $byUser[] = $row;
    $events = [];
    $res2 = $conn->query('SELECT * FROM events');
    while ($row2 = $res2->fetch_assoc()) $events[] = $row2;
    echo json_encode(['total' => $total, 'byUser' => $byUser, 'events' => $events]);
    exit;
}
http_response_code(400);
echo json_encode(['error' => 'Invalid action']);
?>