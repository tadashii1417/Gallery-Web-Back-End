<?php
// required headers
header("Access-Control-Allow-Origin: http://localhost/web/backend/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../objects/user.php';
// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate product object
$user = new User($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));

// validate data
if (!isset($data->username) || !isset($data->password)) {
    http_response_code(422);
    echo json_encode(array("message" => "Unable to create user."));
} elseif (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(array("message" => "Invalid email address"));
}

$user->firstname = $data->firstname;
$user->lastname = $data->lastname;
$user->email = $data->email;
$user->username = $data->username;
$user->password = $data->password;
// TODO: check if username exists.


// create the user
if ($user->create()) {
    http_response_code(200);
    echo json_encode(array("message" => "User was created."));
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create user."));
}
