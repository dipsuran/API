<?php
$vendor_URL = $_SERVER['DOCUMENT_ROOT'] . "/api/vendor/autoload.php";
require $vendor_URL; 

$config = [
    'HOST' => 'localhost',
    'PORT' => 27017,
    'DB' => 'AtplMeet'
];

$servername = "mongodb://{$config['HOST']}:{$config['PORT']}";
$dbname = $config['DB'];

try {
    $client = new MongoDB\Client($servername);
    $db = $client->selectDatabase($dbname);   
    // echo "Connection successful!";
} catch (MongoDB\Driver\Exception\Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}


    // This is Response  Function
function sendResponse($statusCode, $message) {
    http_response_code($statusCode);
    echo json_encode(['status' => $statusCode, 'message' => $message]);
    exit;
}

// validate Required Fields Function
function validateRequiredFields($data, $requiredFields) {
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            sendResponse(400, "$field is required.");
        }
    }
}

?>