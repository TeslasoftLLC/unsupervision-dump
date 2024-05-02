<?php
$fileName = $_GET["file"];

$context = $_GET["context"];

$id = 0;
while (file_get_contents($id."/".$fileName) != "") {
    $id++;
}

$errorFile = file_get_contents(($id-1)."/".$fileName);

// $processedFile = htmlspecialchars($errorFile, ENT_QUOTES, 'UTF-8');

// Your OpenAI API key
$apiKey = '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';

// OpenAI API endpoint
$apiEndpoint = 'https://api.openai.com/v1/chat/completions';

$processedFile = str_replace("\"", "\\\"", $errorFile);

$processedFile2 = str_replace("\n", " ", $processedFile);

$context2 = str_replace("\"", "\\\"", $context);
$context3 = str_replace("\"", "\\\"", $context2);

// Text prompt for chat completion
$prompt = "The following file contains an error. Fix it and write fixed file and nothing else. Don't write any explanations. Don't write any codeblocks. Write your answer in plain text. Use the following additional info:".$context3." File:".$processedFile2;

if ($context == "") {
    $prompt = "The following file contains an error. Fix it and write fixed file and nothing else. Don't write any explanations. Don't write any codeblocks. Write your answer in plain text. File:".$processedFile2;
}

// Data for the API request
$data = <<<EOL
{
    "model": "gpt-4-turbo-preview",
    "messages": [
        {
            "role": "user",
            "content": "$prompt"
        }
    ]
}
EOL;

// header('Content-type: application/json');
// echo($data);
// exit();

// Convert data to JSON format
// $jsonData = json_encode($data);
$jsonData = $data;

// Create cURL resource
$ch = curl_init($apiEndpoint);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey,
));


// Execute cURL session and get the response
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
} else {
    // Decode the JSON response
    $decodedResponse = json_decode($response, true);

    // Output the generated chat completion
    $html = $decodedResponse['choices'][0]['message']['content'];
    
    $html2 = str_replace("\\\"", "\"", $html);
    
    echo $html2;
}

// Close cURL session
curl_close($ch);
?>