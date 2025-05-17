<?php

$fx = $_GET['arg0'];

$sessionKey = $_GET['_session'];

$result = callJsonRpc('SagaApi.setSoundFx', [$fx], $sessionKey);
header('Content-Type: application/json');
echo json_encode($result);

?>