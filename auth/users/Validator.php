<?php
$ip = $_SERVER['REMOTE_ADDR'];
$auth_log = file_get_contents("auth.log");

$username = $_POST['username'];
$password = $_POST['password'];

if ($username == '' || $password == '') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    die('{"code": 400, "message": "Bad request '.$username.'"}');
} else {
    $db = file_get_contents('users.tf');
    $data = json_decode($db);
    
    if (isset($data->{$username}) && $data->{$username} != null && $data->{$username} != "") {
        if ($data->{$username}->{"hash"} == $password) {
            require_once 'jwt_rs384.php';
            $login_time = time();
            $timestamp = date("m/d/Y h:i:s A", $login_time);
            $tmp_log = "[".$timestamp."] [V.A.N.N.I] [INFO]: Authentication succesfull for user '".$username."' from ip ".$ip."\n";
            file_put_contents("auth.log", $auth_log.$tmp_log);
            $t = intval($login_time / (3600 * 24 * 7)); // User must verify credentials every 3 hours
            
            $headers = array('alg'=>'RS384','typ'=>'JWT');
            $payload = array('username'=>$username,'pid'=>$data->{$username}->{'client_id'},'exp'=>($login_time + 3600 * 24 * 7));
            $jwt = generate_jwt($headers, $payload);
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Headers: *");
            echo('{"code": 200, "message": "'.$jwt.'"}');
        } else {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Headers: *");
            die('{"code": 403, "message": "Invalid password"}');
        }
    } else {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
        die('{"code": 404, "message": "User not found"}');
    }
}
?>