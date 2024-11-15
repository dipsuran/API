<?php
$dir = $_SERVER['DOCUMENT_ROOT'] . "/api/db.php";
include_once($dir);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


$rolesCollection = $db->roles;
$usersCollection = $db->users;

if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_URI'] == "/signup") {
    
    // CODE to handle signup api
    $a = $_SERVER['REQUEST_URI'];
    $b = $_SERVER['REQUEST_METHOD'];

    print_r($a);
    print_r($b);
    sendResponse(201, "User data inserted successfully."); 
}


else if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_URI'] == "/login") {
    
    // CODE to handle signup api
    // CODE to handle signup api
    $a = $_SERVER['REQUEST_URI'];
    $b = $_SERVER['REQUEST_METHOD'];

    print_r($a);
    print_r($b);
}

else if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_URI'] == "/logout") {
    
    // CODE to handle signup api
    // CODE to handle signup api
    $a = $_SERVER['REQUEST_URI'];
    $b = $_SERVER['REQUEST_METHOD'];

    print_r($a);
    print_r($b);
}
else
{
    sendResponse(500, "Internal server error."); 
}
?>