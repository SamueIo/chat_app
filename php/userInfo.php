<?php

include_once "config.php";
require "../classes/User.php";
require "../classes/Image.php";
header('Content-Type: application/json');

if (isset($_POST['id'])) {
    $id = $_POST['id'];
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Chyba: ID nebolo poskytnuté'
    ]);
    exit();
}

$message = User::getJSONUserInfo($conn, $id, "user_id, fname, lname, status, last_active");

$imageUser = Image::getProfilePics($conn, $id);

$response = json_decode($message, true);  

$response['profile_pic'] = $imageUser;

echo json_encode($response);

?>
