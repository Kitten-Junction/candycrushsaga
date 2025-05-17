<?php

include $modules . '/candyProperty.php';

$response = getCandyProperties($pdo, $_GET['_session'] ?? null);

echo json_encode($response);

?>
