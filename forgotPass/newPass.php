<?php
$dir = $_SERVER['DOCUMENT_ROOT'] . "/api/db.php";
include_once($dir);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$usersCollection = $db->users;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    validateRequiredFields($data, ['email', 'token', 'password', 'confirm_password']);

    $email = strtolower($data['email']);
    $token = $data['token'];
    $password = $data['password'];
    $confirm_password = $data['confirm_password'];

    if ($password !== $confirm_password) {
        sendResponse(400, 'Passwords do not match.');
    }
    $user = $usersCollection->findOne(['email' => $email]);
    if (!$user) {
        sendResponse(404, 'Email not found.');
    }

    // Check if token is valid (you might store tokens in a separate collection or use a timestamp)
    if ($user['reset_token'] !== $token || strtotime($user['token_expiration']) < time()) {
        sendResponse(400, 'Invalid or expired token.');
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $usersCollection->updateOne(
        ['email' => $email],
        ['$set' => ['password' => $hashedPassword, 'reset_token' => null, 'token_expiration' => null]]
    );

    sendResponse(200, 'Password reset successfully.');
}
?>
