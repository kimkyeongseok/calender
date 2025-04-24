<?php
$host = 'localhost';
$db   = 'scheduler';
$user = 'root';
$pass = '1234';
$charset = 'utf8mb4';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_errno) {
    http_response_code(500);
    echo json_encode(['error' => 'DB 연결 오류']);
    exit;
}
$conn->set_charset($charset);
?>