<?php

$sessionKey = $_GET['_session'];

$result = callJsonRpc('AppCandyCrushAPI.deliverInitialHardCurrencyGift', [], $sessionKey);
header('Content-Type: application/json');
echo json_encode($result);

?>
