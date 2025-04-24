<?php
header('Content-Type: application/json');
require 'db.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$sql = 'SELECT username, role FROM users WHERE username = ? AND password = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $username, $password);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if ($user) {
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    echo json_encode(['success' => false]);
}
$stmt->close();
?>