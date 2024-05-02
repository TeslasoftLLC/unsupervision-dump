<?php
$id = $_GET['id'];

if (file_get_contents($id."-fixed"."/manifest.json") == "") {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    die('{"code": 400, "message": "Update not found"}');
} else {
    $versions = file_get_contents("fixes.json");
    $decoded_versions = json_decode($versions);
    
    $d = array();
    
    for ($i = 0; $i < sizeof($decoded_versions->{"updates"}); $i++) {
        if ($decoded_versions->{"updates"}[$i] != strval($id)) {
            array_push($d, $decoded_versions->{"updates"}[$i]);
        }
    }
    
    $decoded_versions->{"updates"} = $d;
    $encoded_versions = json_encode($decoded_versions);
    
    rrmdir($id."-fixed");
    
    file_put_contents("fixes.json", $encoded_versions);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    echo('{"code": 200, "message":"'.$id.'"}');
}

function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir."/".$object) == "dir") 
                    rrmdir($dir."/".$object); 
                else unlink($dir."/".$object);
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

?>