<?php
include_once "config.php";
require "../classes/Messages.php";
header('Content-Type: application/json');

if (isset($_POST['chat_id']) && isset($_POST['id'])) {
    $chat_id = $_POST['chat_id'];
    $id = $_POST['id'];
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing chat_id or id in request'
    ]);
    exit;
}

// Zavoláme funkciu na získanie nových správ po ID
$latestMess = Messages::getLatestMessages($conn, $chat_id, $id);

// Logovanie výstupu pre kontrolu
error_log("latestMess: " . var_export($latestMess, true));

if ($latestMess) {
    $decodedMess = json_decode($latestMess, true);
    
    if (isset($decodedMess['status']) && $decodedMess['status'] === 'success' && isset($decodedMess['messages']) && count($decodedMess['messages']) > 0) {
        // Získať všetky nové správy
        $messages = $decodedMess['messages'];

        if (!empty($messages)) {
            $response['getLatestMessages'] = [
                'status' => 'success',
                'messages' => $messages 
            ];
        } else {
            $response['getLatestMessages'] = [
                'status' => 'no_new_messages',
                'message' => 'No new messages'
            ];
        }
    } else {
        $response['getLatestMessages'] = [
            'status' => 'no_new_messages',
            'message' => 'No new messages'
        ];
    }
} else {
    $response['getLatestMessages'] = [
        'status' => 'error',
        'message' => 'Something went wrong'
    ];
}

echo json_encode($response);
