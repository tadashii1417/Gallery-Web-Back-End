<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// required to encode json web token
include_once '../../config/core.php';
include_once '../../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../../libs/php-jwt-master/src/ExpiredException.php';
include_once '../../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../../libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

include_once '../../config/database.php';
include_once '../../objects/image.php';
include_once '../../objects/user.php';

#Get input jwt
$data = json_decode(file_get_contents("php://input"));

#Create connection to database
$database = new Database();
$db = $database->getConnection();

#Create user object to get check user data
$user = new User($db);

#Check the input
$jwt = isset($data->jwt) ? $data->jwt : "";

if ($jwt) {
    try {
        #Decode jwt to get user information
        $decoded = JWT::decode($jwt, $key, ['HS256']);

        #Get Username
        $user->username = $decoded->data->username;

        #Check if the username exits
        $check_if_user_exit = $user->usernameExists();

        #If the username exits, let get the images
        if ($check_if_user_exit) {

            #Create object image
            $all_image = new Image($db);
            #Call function from image object.
            $all_image_return = $all_image->get_all_images();

            if ($all_image_return) {
                // foreach ($all_image_return as $r_image) {
                //     $temp_user = new User($db);
                //     $user->id = 
                // }
                $size_of_list_image = sizeof($all_image_return);

                for ($temp_count = 0; $temp_count < $size_of_list_image; $temp_count++) {
                    $temp_user = new User($db);
                    $user->id = $all_image_return[$temp_count]['user_id'];
                    $temp_owner = $user->get_owner_info();
                    $all_image_return[$temp_count] = array($temp_owner, $all_image_return[$temp_count]);
                    unset($temp_user);
                }

                #If it can get some images, retutn it.

                #echo json_encode($all_image_return[0]);

                echo json_encode(["images" => $all_image_return]);
            } else {
                #If it can not get anything, return notice.
                echo json_encode(["message" => "can't fetch images"]);
            }
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode([
            "message" => "Access denied.",
            "error" => $e->getMessage(),
        ]);
    }
} else {
    http_response_code(401);
    echo json_encode(["message" => "Access denied."]);
}
