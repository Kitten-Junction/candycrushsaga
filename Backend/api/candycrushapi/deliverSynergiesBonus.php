<?php
include $modules . '/user/userItems.php';
$sessionKey = $_GET['_session'] ?? '';

$userId = getUserIdBySessionKey($pdo, $sessionKey);
if ($userId === null) {
    http_response_code(403);
    exit;
}

synergieBonus($pdo, $sessionKey);
?>