<?php
$id = $_GET['id'];

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
echo(file_get_contents($id."/manifest.json"));
?>