<?php
include_once "config.php";
require "../classes/Messages.php";
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
session_start();
$loggedUserId = $_SESSION['user_id'];
if (isset($data['userId']) && isset($data['messageId'])) {
    $user_id = $data['userId'];
    $message_id = $data['messageId']; 

    $messageRead = Messages::markMessageAsRead($conn, $message_id, $loggedUserId);

    error_log("messageRead: " . var_export($messageRead, true));

    if ($messageRead) {
        $response = [
            'status' => 'success',
            'message' => 'Správa marked as read.'
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Sprava wasnt marked as read.'
        ];
    }
} else {
    $response = [
        'status' => 'error',
        'message' => 'Missing userId or messageId in request'
    ];
}

echo json_encode($response);
exit();
?>
