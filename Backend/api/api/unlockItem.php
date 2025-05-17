<?php

include $modules . '/user/userItems.php';

if (isset($_GET['arg0']) && isset($_GET['_session'])) {
    $type = $_GET['arg0'];
    $sessionKey = $_GET['_session'];

    unlockItem($pdo, $type, $sessionKey);
} else {
    http_response_code(400);
}

?>
