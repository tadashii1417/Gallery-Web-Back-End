<?php
// required headers
include_once '../../config/header.php';
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
    http_response_code(400);
    echo json_encode(array("message" => "Missing username or password."));
    return;
} elseif (strlen($data->username) < 6) {
    http_response_code(400);
    echo json_encode(array("message" => "Username must be at least 6 characters in length"));
    return;
} elseif (strlen($data->password) < 6) {
    http_response_code(400);
    echo json_encode(array("message" => "Password must be at least 6 characters in length"));
    return;
} elseif (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(array("message" => "Invalid email address"));
    return;
} 

// check if username exists (LOL should have set username as PRIMARY KEY)

$stmt = $db->prepare('SELECT * FROM users WHERE username = :username');
$stmt->bindParam(':username', $data->username);
if ($stmt->execute()) {
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!empty($result)) {
        http_response_code(409);
        echo json_encode(array("message" => "Username already exists"));
        return;
    }
} else {
    http_response_code(500);
    echo json_encode(array("message" => "Operation couldn't be completed"));
    return;
}

$user->firstname = $data->firstname;
$user->lastname = $data->lastname;
$user->email = $data->email;
$user->username = $data->username;
$user->password = $data->password;



// create the user
if ($user->create()) {
    http_response_code(200);
    echo json_encode(array("message" => "User was created."));
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create user."));
}
