<?php
$token = $_GET['token'];
$ip = $_SERVER['REMOTE_ADDR'];
$auth_log = file_get_contents("auth.log");

$err = <<<EOL
<!DOCTYPE html>
<html>
    <head>
        <meta charset = "utf-8">
        <style>
            * {
                padding: 0;
                margin: 0;
            }
            
            html, body {
                background-color: #212121;
                color: #fff;
            }
        </style>
    </head>
    <body>
        <p>Please, wait...</p>
        <p>If you are not redirected please enable JavaScript</p>
        <script>
            (function() {
                window.location.replace("https://unsupervision.teslasoft.org/");
            })()
        </script>
    </body>
</html>
EOL;

if ($token == "") {
    die("Access denied!");
} else {
    require_once 'jwt_rs384.php';
    
    if (is_jwt_valid($token)) {
        $adapter = file_get_contents("idadapter.tf");
        
        $adapter_data = json_decode($adapter);
        
        $token_data = explode('.', $token);
        
        $payload = base64_decode($token_data[1]);
        
        $json_payload = json_decode($payload);
        
        $id5_id = $json_payload->{'client'};
        
        $username = "";
        
        for ($i = 0; $i < sizeof($adapter_data); $i++) {
            if ($adapter_data[$i]->{'id5'} == $id5_id) {
                $username = $adapter_data[$i]->{'service_login'};
                break;
            }
        }
        
        if ($username == "") {
            die($err);
        } else {
            $login_time = time();
            $timestamp = date("m/d/Y h:i:s A", $login_time);
            $tmp_log = "[".$timestamp."] [ID5] [INFO]: Authentication succesfull for user '".$username."' from ip ".$ip."\n";
            file_put_contents("auth.log", $auth_log.$tmp_log);
            $t = intval($login_time / (3600 * 3)); // User must verify credentials every 3 hours
            
            $headers = array('alg'=>'RS384','typ'=>'JWT');
            $payload = array('username'=>$username,'pid'=>$id5_id,'exp'=>($login_time + 3600 * 3));
            $jwt = generate_jwt($headers, $payload);
            
$app = <<<EOL
<!DOCTYPE html>
<html>
    <head>
        <title>Authenticating...</title>
        <meta charset = "utf-8">
        <style>
            * {
                padding: 0;
                margin: 0;
            }
            
            html, body {
                background-color: #212121;
                color: #fff;
            }
        </style>
    </head>
    <body>
        <p>Authenticated</p>
        <p>Please, wait...</p>
        <p>If you are not redirected please enable JavaScript</p>
        <script>
            (function() {
                localStorage.setItem("token", "$jwt");
                window.location.replace("https://unsupervision.teslasoft.org/");
            })()
        </script>
    </body>
</html>
EOL;
            echo($app);
        }
    } else {
        die($err);
    }
}
?>