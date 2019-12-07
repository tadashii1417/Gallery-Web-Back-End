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
    $query = 'SELECT * FROM collections
    WHERE (user_id = :user_id)';
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $_GET['user_id']);
    if ($stmt->execute()) {
        $ret = array();
        while ($row = $stmt->fetchObject()) {
            $ret[] = $row;
        }
        echo json_encode(["collections" => $ret]);
    } else {
        http_response_code(400);
        echo json_encode([
            "message" => "Can't fetch collections.",
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "message" => "Can't fetch collections.",
        "error" => $e->getMessage(),
    ]);
}
