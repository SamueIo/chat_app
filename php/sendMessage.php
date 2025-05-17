<?php

include_once "config.php";
require "../classes/Messages.php";
require "../classes/User.php";

session_start();
$loggedInUserId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $message = isset($_POST['message']) ? $_POST['message'] : '';  // Správa môže byť prázdna
    $chat_id = $_POST['chat_id'];
    $user_id = $_POST['user_id'];
    $parent_message_id = $_POST['parent_message_id'];


    $files = isset($_FILES['files']) ? $_FILES['files'] : [];

    if (empty($message) && empty($files)) {
        echo json_encode(['status' => 'error', 'message' => 'Chýbajúce údaje alebo súbory']);
        exit;
    }

    $response = Messages::sendMessage($conn, $chat_id, $user_id, $message, $parent_message_id, $files);
    
    if (json_decode($response)->status === 'success') {
        $status = 'Aktívny/a teraz';
        User::updateUserStatus($conn, $loggedInUserId, $status);
    }

    echo $response;
}
