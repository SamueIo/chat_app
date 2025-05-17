<?php
include_once "config.php";
require "../classes/User.php";
header('Content-Type: application/json');

$input = file_get_contents("php://input");

$data = json_decode($input, true);

if (isset($data['user_id'])) {
    $userId = $data['user_id'];  

    if (User::updateLastActive($conn, $userId)) {
        $msg = 'Successfully updated user ';
        $status = 'success';
    } else {
        $msg = 'Something went wrong in updating user';
        $status = 'error';
    }
} else {
    $msg = 'Missing user_id';
    $status = 'error';
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    $status = 'offline';

    require_once 'db_connection.php';

    User::updateUserStatus($conn, $user_id, $status);
}


echo json_encode(['status' => $status, 'msg' => $msg]);
