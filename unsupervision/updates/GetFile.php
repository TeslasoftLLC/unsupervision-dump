<?php
$updateId = $_GET['id'];
$path = $_GET['path'];
$fileName = $_GET['file'];

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
echo(file_get_contents($updateId.$path.$fileName));
?>