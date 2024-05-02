<?php
$fileName = $_GET['file'];

$id = 0;
while (file_get_contents($id."/".$fileName) != "") {
    $id++;
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
echo(file_get_contents(($id-1)."/".$fileName));
?>