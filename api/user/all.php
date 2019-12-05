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

$query = "SELECT * FROM users";

$stmt = $db->prepare($query);

try {
    $stmt->execute();
    $ret = array();
    while ($row = $stmt->fetchObject()) {
        $user = new stdClass();
        $user->username = $row->username;
        $user->firstname = $row->firstname;
        $user->lastname = $row->lastname;
        $user->description = $row->description;
        $user->email = $row->email;
        $user->status = $row->status;
        $ret[] = $user;
    }
    echo json_encode(array("users" => $ret));
}
catch(Exception $e) {
    http_response_code(401);
    echo json_encode(array("message" => "Unable to fetch users."));    
}