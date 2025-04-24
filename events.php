<?php
header('Content-Type: application/json');
require 'classes.php';

$evtMgr = new EventManager();
$action = $_REQUEST['action'] ?? '';

if ($action === 'list') {
    $res = $evtMgr->listEvents(
        $_GET['page'] ?? 1,
        $_GET['pageSize'] ?? 10
    );
    echo json_encode($res);
    exit;
}
if ($action === 'get') {
    $event = $evtMgr->getEvent((int)$_GET['id']);
    echo json_encode(['event' => $event]);
    exit;
}
if ($action === 'save') {
    $evt = json_decode($_POST['event'], true);
    $result = $evtMgr->saveEvent($evt, $_POST['user'] ?? null);
    echo json_encode($result);
    exit;
}
if ($action === 'delete') {
    $ok = $evtMgr->deleteEvent((int)$_POST['id']);
    echo json_encode(['success' => $ok]);
    exit;
}
if ($action === 'copy') {
    $result = $evtMgr->copyEvent((int)$_POST['id']);
    echo json_encode(['success' => (bool)$result]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid action']);
exit;
?>