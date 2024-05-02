<?php

function generate_jwt($headers, $payload) {
    $headers_encoded = base64url_encode(json_encode($headers));
    $payload_encoded = base64url_encode(json_encode($payload));
    $private_key = file_get_contents("/home/teslasof/id.teslasoft.org/protected/cred/rsa.key");
    $public_key = file_get_contents("/home/teslasof/id.teslasoft.org/protected/cred/rsa.pub");
    $signature = "";
    openssl_sign($headers_encoded.".".$payload_encoded, $signature, $private_key, "sha384WithRSAEncryption");
    $signature_encoded = base64url_encode($signature);
    $jwt = "$headers_encoded.$payload_encoded.$signature_encoded";
    return $jwt;
}

function is_jwt_valid($jwt) {
    $tokenParts = explode('.', $jwt);
    $header = base64_decode($tokenParts[0]);
    $payload = base64_decode($tokenParts[1]);
    $signature_provided = $tokenParts[2];

    $expiration = json_decode($payload)->exp; // Your variable here
    $is_token_expired = ($expiration - time()) < 0;

    $base64_url_header = base64url_encode($header);
    $base64_url_payload = base64url_encode($payload);
    $private_key = file_get_contents("/home/teslasof/id.teslasoft.org/protected/cred/rsa.key");
    $public_key = file_get_contents("/home/teslasof/id.teslasoft.org/protected/cred/rsa.pub");

    $sig = openssl_verify("$base64_url_header.$base64_url_payload", base64url_decode($signature_provided), $public_key, OPENSSL_ALGO_SHA384);

    if ($sig == 1 && !$is_token_expired) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function base64url_encode($str) {
    return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
}

function base64url_decode($data) {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

?>