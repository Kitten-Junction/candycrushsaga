<?php

$sessionKey = $_GET['_session'];

$result = callJsonRpc('AppSagaApi.getAllItems', [], $sessionKey);
header('Content-Type: application/json');
echo json_encode($result);

?>
