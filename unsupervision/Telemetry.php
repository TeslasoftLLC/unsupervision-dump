<?php

$telemetry_db = file_get_contents("telemetry.json");

$telemetry_decoded = json_decode($telemetry_db);

$data = $_GET['data'];

if ($data == "") {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    die('{"code": 400, "message": "Bad request"}');
} else {
    $data_decrypted = base64_decode($data);
    $data_decoded = json_decode($data_decrypted);
    array_push($telemetry_decoded, $data_decoded);
    $telemetry_encoded = json_encode($telemetry_decoded);
    file_put_contents("telemetry.json", $telemetry_encoded);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    echo('{"code": 200, "message": "OK"}');
}

?>