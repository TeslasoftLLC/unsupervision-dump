<?php
file_put_contents("telemetry.json", "[]");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
echo('{"code": 200, "message": "OK"}');
?>