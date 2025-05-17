<?php

include $modules . '/user/userItems.php';

if (isset($_GET['arg0']) && isset($_GET['_session'])) {
    $inputJson = $_GET['arg0'];
    $sessionKey = $_GET['_session'];

    handOutItemWinnings($pdo, $inputJson, $sessionKey);
} else {
    http_response_code(400);
}


?>
