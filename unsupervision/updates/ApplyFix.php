<?php
$id = $_GET['id'];

if (file_get_contents($id."-fixed"."/manifest.json") == "") {
    header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
    die('{"code": 400, "message": "Update not found"}');
} else {
    $versions = file_get_contents("versions.json");
    $decoded_versions = json_decode($versions);
    array_push($decoded_versions->{"updates"}, $id);
    $encoded_versions = json_encode($decoded_versions);
    
    $fixes = file_get_contents("fixes.json");
    $decoded_fixes = json_decode($fixes);
    
    $d = array();
    
    for ($i = 0; $i < sizeof($decoded_fixes->{"updates"}); $i++) {
        if ($decoded_fixes->{"updates"}[$i] != strval($id)) {
            array_push($d, $decoded_fixes->{"updates"}[$i]);
        }
    }
    
    $decoded_fixes->{"updates"} = $d;
    $encoded_fixes = json_encode($decoded_fixes);
    
    if (rename($id."-fixed", $id)) {
        file_put_contents("versions.json", $encoded_versions);
        file_put_contents("fixes.json", $encoded_fixes);
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
        echo('{"code": 200, "message":"'.$id.'"}');
    } else {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
        echo('{"code": 500, "message":"Could not apply the update"}');
    }
}

?>