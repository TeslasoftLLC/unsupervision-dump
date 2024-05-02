<?php
$token = $_POST['token'];

if ($token == '') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    die('{"code": 400, "message": "Bad request"}');
} else {
    require_once 'jwt_rs384.php';
    
    if (is_jwt_valid($token)) {
        $token_data = explode('.', $token);
        $payload = base64_decode($token_data[1]);
        $json_payload = json_decode($payload);
        $username = $json_payload->{'username'};
        
        $db = file_get_contents('users.tf');
        $data = json_decode($db);
        
        if (isset($data->{$username}) && $data->{$username} != null && $data->{$username} != "") {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Headers: *");
            echo('{"code": 200, "message": "Session is valid"}');
        } else {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Headers: *");
            die('{"code": 404, "message": "User not found"}');
        }
    } else {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
        die('{"code": 403, "message": "Invalid token"}');
    }
}
?>