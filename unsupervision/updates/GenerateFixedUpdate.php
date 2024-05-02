<?php

$update_encoded = $_POST['u'];

$versions = file_get_contents("fixes.json");



$decoded_versions = json_decode($versions);

$id = 0;
while (file_get_contents($id."/manifest.json") != "") {
    $id++;
}

while (file_get_contents($id."-fixed"."/manifest.json") != "") {
    $id++;
}

mkdir($id."-fixed");


array_push($decoded_versions->{"updates"}, strval($id));

$encoded_versions = json_encode($decoded_versions);



$payload = base64_decode($update_encoded);

$payload_decoded = json_decode($payload);

$files = array();

$pathMap = $payload_decoded->{'filePathMap'};
$fileList = $payload_decoded->{'fileList'};
$fileContents = $payload_decoded->{'files'};

for ($i = 0; $i < sizeof($fileList); $i++) {
    mkdir($id."-fixed".$pathMap[$i]);
    
    file_put_contents($id."-fixed".$pathMap[$i].$fileList[$i], base64_decode($fileContents[$i]->{"content"}));
    
    $tmp = array(
        "fileName" => $fileList[$i],
        "path" => $pathMap[$i],
        "updateMethod" => "add",
        "version" => "1.0",
        "sha256" => hash('sha256', base64_decode($fileContents[$i]->{"content"}))
    );
    
    array_push($files, $tmp);
}

$manifest = array(
    "version" => "1.0",
    "timestamp" => time(),
    "updateId" => strval($id),
    "fileList" => $payload_decoded->{'fileList'},
    "changeMap" => $files
);

$manifest_encoded = json_encode($manifest);

file_put_contents($id."-fixed"."/manifest.json", $manifest_encoded);
file_put_contents("fixes.json", $encoded_versions);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
echo('{"code": 200, "message":"'.$id.'"}');
?>
