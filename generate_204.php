<?php
$time = time();
header('timestamp: '.$time);
header('x-status: OK');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header("HTTP/1.1 204 No content");
?>