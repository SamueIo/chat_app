<?php
include_once "config.php";
require "../classes/Messages.php";
header('Content-Type: application/json');

$user1_id = isset($_POST['user1_id']) ? (int) $_POST['user1_id'] : 0;
$user2_id = isset($_POST['user2_id']) ? (int) $_POST['user2_id'] : 0;

if ($user1_id <= 0 || $user2_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Neplatné ID používateľov."]);
    exit;
}

$chat_id = Messages::createChat($conn, $user1_id, $user2_id);

$response = [];

if ($chat_id) {
    $response['create_chat'] = [
        "status" => "success",
        "chat_id" => $chat_id 
    ];

    // Otvorenie chatu
    $openChatResponse = Messages::openChat($conn, $user1_id, $user2_id);

    $openChatDecoded = json_decode($openChatResponse, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $response['open_chat'] = $openChatDecoded;
    } else {
        $response['open_chat'] = [
            "status" => "error",
            "message" => "Chyba pri otváraní chatu."
        ];
    }

} else {
    $response['create_chat'] = [
        "status" => "error",
        "message" => "Nepodarilo sa vytvoriť chat."
    ];
}

// Získanie správ aj s médiami
$messages = Messages::getMessages($conn, $chat_id);

if ($messages) {
    $response['messages'] = $messages; 
} else {
    $response['messages'] = []; 
}

echo json_encode($response);
?>
