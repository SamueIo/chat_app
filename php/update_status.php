<?php
include_once "config.php";
require "../classes/User.php";
header('Content-Type: application/json');

session_start();
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['status'])) {
        $status = $data['status'];

        if (User::updateUserStatus($conn, $user_id, $status)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "DB update failed"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Missing parameters"]);
    }
}