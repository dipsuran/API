<?php
$dir = $_SERVER['DOCUMENT_ROOT'] . "/api/db.php";
include_once($dir);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$usersCollection = $db->users;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true); 

    if (empty($data['email'])) {
        sendResponse(400, 'email is required.');
    }

    $email = $data['email'];

    $user = $usersCollection->findOne(['email' => $email]);

    if (!$user) {
        sendResponse(401, 'email not found.'); 
    }

    switch ($user['status']) {
        case 1:
            $usersCollection->updateOne(
                ['email' => $email],
                ['$set' => ['status' => 0]]
            );
            sendResponse(200, 'Logout successful. Your account has been deactivated.');

        case 0:
            sendResponse(200, 'Logout successful. Your account is already inactive.');

        default:
            sendResponse(403, 'Account status is invalid.');
    }
}

?>