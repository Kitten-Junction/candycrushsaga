<?php
include $modules . '/user/userData.php';
include $modules . '/KingMessages.php';
include $modules . '/eventManager.php';

$sessionKey = $_GET['_session'] ?? '';
$userData = getCurrentUser($pdo, $sessionKey);
$userId = getUserIdBySessionKey($pdo, $sessionKey);

$events = getUserMessages($pdo, $userId);

$poll = [
    "currentUser" => $userData,
    "events" => $events
];

header('Content-Type: application/json');
echo json_encode($poll);
?>
