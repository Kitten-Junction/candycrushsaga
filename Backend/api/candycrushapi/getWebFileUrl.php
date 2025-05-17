<?php

$jsonfile = $_GET['arg0'];
$sessionKey = $_GET['_session'];

$result = callJsonRpc('AppCandyCrushAPI.getJsonFileUrl', [$jsonfile], $sessionKey);
header('Content-Type: application/json');
echo '"' . $result . '"';

?>
