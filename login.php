<?php
// ¿¹½Ã: login.php
header('Content-Type: application/json');
require 'classes.php';

$userMgr = new UserManager();
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$user = $userMgr->authenticate($username, $password);

echo json_encode([
    'success' => (bool)$user,
    'user'    => $user
]);
exit;
?>