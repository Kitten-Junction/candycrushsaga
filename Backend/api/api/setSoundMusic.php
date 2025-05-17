<?php

$sound = $_GET['arg0'];

$sessionKey = $_GET['_session'];

$result = callJsonRpc('SagaApi.setSoundMusic', [$sound], $sessionKey);
header('Content-Type: application/json');
echo json_encode($result);

?>