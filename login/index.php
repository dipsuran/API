<?php
$dir = $_SERVER['DOCUMENT_ROOT'] . "/api/db.php";
include_once($dir);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$usersCollection = $db->users;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true); 

    validateRequiredFields($data, ['email', 'password']);

    $email = $data['email'];
    $password = $data['password'];

    $user = $usersCollection->findOne(['email' => $email]);

    if (!$user) {
        sendResponse(401, 'Invalid email or password.'); 
    }

    if (!password_verify($password, $user['password'])) {
        sendResponse(401, 'Invalid email or password.'); 
    }

    if ($user['status'] == 0) {

        $usersCollection->updateOne(
            ['email' => $email],
            ['$set' => ['status' => 1]]
        );
        sendResponse(200, 'Login successful. Your account is now active.'); 

    } else if ($user['status'] == 1) {
        sendResponse(200, 'Login successful. Your account is already active.'); 
    } else {
        sendResponse(403, 'Account status not valid.');
    }
}

?>
