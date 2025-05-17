<?php

include $modules . '/user/userData.php';
include $modules . '/KingMessages.php';
include $modules . '/eventManager.php';

$sessionKey = $_GET['_session'] ?? '';
$userId = getUserIdBySessionKey($pdo, $sessionKey);

$event = processSingleMessage($pdo, $userId);

$poll = [];

if ($event) {
    KingLifeSystem::KingGameWin($pdo, $userId);

    $userData = getCurrentUser($pdo, $sessionKey);

    $poll['currentUser'] = $userData;
}

header('Content-Type: application/json');
echo json_encode($poll);

?>
