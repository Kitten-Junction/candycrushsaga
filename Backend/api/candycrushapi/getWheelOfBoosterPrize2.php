<?php
$sessionKey = $_GET['_session'];

$result = callJsonRpc('AppCandyCrushAPI.getWheelOfBoosterPrize', [], $sessionKey);

header('Content-Type: application/json');
echo '"' . $result . '"';
?>
