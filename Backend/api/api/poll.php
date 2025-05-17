<?php
include $modules . '/user/userData.php';

$sessionKey = $_GET['_session'];

$userData = getCurrentUser($pdo, $sessionKey);

$poll = [
    "currentUser" => $userData    
];

header('Content-Type: application/json');
echo json_encode($poll);

?>
