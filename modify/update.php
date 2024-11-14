<?php
$dir = $_SERVER['DOCUMENT_ROOT'] . "/api/db.php";
include_once($dir);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

$rolesCollection = $db->roles;
$usersCollection = $db->users;

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    
    $data = json_decode(file_get_contents('php://input'), true);

    validateRequiredFields($data, ['_id','username', 'email', 'password']);

    $userId = $data['_id'];
    
    $updateFields = [];
    if (!empty($data['username'])) {
        $username = $data['username'];
        $updateFields['username'] = $username;
    }

    if (!empty($data['email'])) {
        $email = $data['email'];
        if ($usersCollection->findOne(['email' => $email, '_id' => ['$ne' => new MongoDB\BSON\ObjectId($userId)]])) {
            sendResponse(409, 'Email already exists.');
        }
        $updateFields['email'] = $email;
    }

    if (!empty($data['password'])) {
        $password = $data['password'];
        $updateFields['password'] = password_hash($password, PASSWORD_BCRYPT);
    }

    if (isset($data['roles'])) {
        $roles = $data['roles'];
        $rolesObjs = $rolesCollection->find(['name' => ['$in' => $roles]])->toArray();
        $roleIds = [];
        foreach ($rolesObjs as $role) {
            $roleIds[] = $role['_id'];
        }
        $updateFields['roles'] = $roleIds;
    }

    if (empty($updateFields)) {
        sendResponse(400, 'No valid fields to update.');
    }

    try {
        $result = $usersCollection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($userId)], 
            ['$set' => $updateFields] 
        );

        if ($result->getModifiedCount() > 0) {
            sendResponse(200, 'User updated successfully.');
        } else {
            sendResponse(404, 'User not found or no changes detected.');
        }
    } catch (Exception $e) {
        sendResponse(500, 'Failed to update user: ' . $e->getMessage());
    }
}

?>
