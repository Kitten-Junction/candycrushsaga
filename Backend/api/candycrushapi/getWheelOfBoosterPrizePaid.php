<?php

include $modules . '/user/userData.php';
include $modules . '/user/userItems.php';
include $modules . '/KingIAP.php';

$result = PaidBoosterWheel($pdo, $_GET['_session'] ?? '');

http_response_code($result['status']);

if ($result['success'] && $result['prize'] !== null) {
    header('Content-Type: application/json');
    echo '"' . $result['prize'] . '"';
}

?>
