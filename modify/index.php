<?php
$dir = $_SERVER['DOCUMENT_ROOT'] . "/api/db.php";
include_once($dir);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

$usersCollection = $db->users;
$rolesCollection = $db->roles; 

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        if (preg_match('/^[a-f0-9]{24}$/', $id)) {
            $user = $usersCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);

            if ($user) {
                $user->_id = (string) $user->_id;

                $roleIds = $user->roles; 
                $roleNames = [];

                foreach ($roleIds as $roleId) {
                    $role = $rolesCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($roleId)]);
                    if ($role) {
                        $roleNames[] = $role->name; 
                    }
                }

                $user->roles = $roleNames;
                unset($user->status);

                echo json_encode($user);
            } else {
                sendResponse(404, "User not found");
            }
        } else {
            sendResponse(400, "Invalid ID format");
        }
    } else {
        sendResponse(400, "ID parameter is required");
    }
}
?>
