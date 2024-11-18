<?php
$dir = $_SERVER['DOCUMENT_ROOT'] . "/api/db.php";
include_once($dir);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$usersCollection = $db->users;
$rolesCollection = $db->roles; 


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = json_decode(file_get_contents('php://input'), true);

    validateRequiredFields($data, ['email']);

    $email = strtolower($data['email']);

    $user = $usersCollection->findOne(['email' => $email]);
    if (!$user) {
        sendResponse(404, 'Email not found.');
    }

    $token = bin2hex(random_bytes(16));
    $expires = new DateTime();
    $expires->add(new DateInterval('PT01H')); 
    $usersCollection->updateOne(
        ['email' => $email],
        ['$set' => ['reset_token' => $token, 'reset_expires' => $expires->format('Y-m-d H:i:s')]]
    );

    // Send the email (this is a placeholder; implement your email sending logic here)
    $resetLink = "http://localhost/reset-password.php?token=$token&email=$email";
    $subject = 'Password Reset Request';
    $message = "To reset your password, click the link below:\n$resetLink";
    mail("dipsurani216@gmail.com", $subject, $message); 

    sendResponse(200, 'Password reset link has been sent to your email.');
}
?>
