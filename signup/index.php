<?php
$dir = $_SERVER['DOCUMENT_ROOT'] . "/api/db.php";
include_once($dir);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


$rolesCollection = $db->roles;
$usersCollection = $db->users;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = json_decode(file_get_contents('php://input'), true); 

    // Validate required fields
    validateRequiredFields($data, ['username', 'email', 'password']);

    $username = $data['username'];
    $email = strtolower($data['email']);
    $password = $data['password'];
    $roles = isset($data['roles']) ? $data['roles'] : ['user']; 


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendResponse(400, 'Invalid email format.');
    }
    
    if ($usersCollection->findOne(['email' => $email])) {
        sendResponse(409, 'Email already exists.');
    }
    if ($usersCollection->findOne(['username' => $username])) {
        sendResponse(409, 'Username already exists.');
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $status = 0;
    $user = [
        'username' => $username,
        'email' => $email,
        'password' => $hashedPassword,
        'status' => $status,
        'roles' => []
    ];

    // Assign roles to the user
    $rolesObjs = $rolesCollection->find(['name' => ['$in' => $roles]])->toArray();
    foreach ($rolesObjs as $role) {
        $user['roles'][] = $role['_id'];
    }

    try {
        $usersCollection->insertOne($user);
        sendResponse(201, "User data inserted successfully."); 
    } catch (Exception $e) {
        sendResponse(500, 'Failed to insert user: ' . $e->getMessage());
    }
}
?>