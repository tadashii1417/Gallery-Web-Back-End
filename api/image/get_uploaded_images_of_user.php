<?php
// required headers
include_once '../../config/header.php';
include_once '../../config/database.php';
include_once '../../objects/user.php';

$database = new Database();
$db = $database->getConnection();

// $data = json_decode(file_get_contents("php://input"));
if (empty($_GET['user_id'])) {
    http_response_code(400);
    echo (json_encode(['message' => 'missing user_id']));
    return;
}

try {
    $query = 'SELECT * FROM images
    WHERE (user_id = :user_id) AND status = 1';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $_GET['user_id']);
    if ($stmt->execute()) {
        $ret = array();
        while ($row = $stmt->fetchObject()) {
            $temp_user = new User($db);
            $temp_user->id = $row->user_id;
            $row->owner = $temp_user->get_owner_info();
            $ret[] = $row;
        }
        echo json_encode(["images" => $ret]);
    } else {
        http_response_code(400);
        echo json_encode([
            "message" => "Can't fetch images.",
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "message" => "Can't fetch images.",
        "error" => $e->getMessage(),
    ]);
}
