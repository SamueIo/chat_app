<?php 
session_start();
require "../classes/User.php";
require "../classes/Messages.php";
require "../classes/Image.php";
include_once "config.php";

$loggedUserId = $_SESSION['user_id'];

$result_info = User::getEveryUserInfo($conn, $columns = "*");

$output = "";
$openChatIdStr = "";
$num_rows = count($result_info);

$displayedChats = [];

if ($num_rows == 0) {
    $output .= "Žiadny dostupný používateľ";
} elseif ($num_rows > 0) {
    foreach ($result_info as $row) {
        $userId = $row['user_id'];

        $openChatIdStr = [$loggedUserId, $userId];
        sort($openChatIdStr);
        $openChatIdStr = implode('', $openChatIdStr);

        if (in_array($openChatIdStr, $displayedChats)) {
            continue;  
        }

        $displayedChats[] = $openChatIdStr;

        $result_message = Messages::getLatestSingleMessage($conn, $openChatIdStr);

        $messageForUser = '';
        $filePath = null; 
        $fileType = '';   
        $readStatusClass = '';
        if ($result_message) {
            $messageForUser = $result_message['message'];
            $readAtForUser = $result_message['read_at'];  

            if (isset($result_message['file_path'])) {
                $filePath = $result_message['file_path']; 
                
                if ($filePath) {
                    $fileInfo = pathinfo($filePath);
                    $fileExtension = strtolower($fileInfo['extension']);
                    
                    if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $fileType = 'image';
                    } elseif (in_array($fileExtension, ['mp4', 'avi', 'mov'])) {
                        $fileType = 'video';
                    } elseif (in_array($fileExtension, ['mp3', 'wav', 'ogg'])) {
                        $fileType = 'audio';
                    }
                }
            }

            if (isset($result_message['read_at']) && $result_message['read_at'] != null) {
                $readStatusClass = 'read'; 
            } else {
                $readStatusClass = 'unread'; 
            }
        }

        $photos = Image::getProfilePics($conn, $userId);
        $profileClass = '';
        $messageClass = 'user-last-message';

        if (isset($result_message['user_id']) && $result_message['user_id'] == $loggedUserId) {
            $profileClass = ''; 
            $messageClass = 'user-last-message';  
        } else if ($result_message && $readStatusClass === 'unread' && $result_message['user_id'] != $loggedUserId) {

            $profileClass = ''; 
            $messageClass = 'user-last-message messageRead'; 
        }

        if ($row['status'] == "Aktívny/a teraz") {
            $statusInfo = '<i class="fa-solid fa-circle" style="color:green;"></i>';
        } else if ($row['status'] == "away") {
            $statusInfo = '<i class="fa-solid fa-circle" style="color:brown;"></i>';
        }else{
            $statusInfo = '<i class="fa-regular fa-circle" style="color:silver;"></i>';
        }
        
        
        
        
        if ($messageForUser == '' && isset($result_message['user_id']) && $loggedUserId == $result_message['user_id']) {
            $messageForUserShow = 'Poslali ste média';
        
        }else if(!isset($readAtForUser) && empty($result_message['created_at'])  ){

            $messageForUserShow = 'Žiadne správy';
        }else if ( $messageForUser == ''){
            $messageForUserShow = 'Použivateľ poslal/a média';
        }else{
            $messageForUserShow = $messageForUser;
        }
        
        // Generovanie HTML pre profil používateľa
        $output .= '<div class="logged-user user" data-user-id="' . $row['user_id'] . '">
                        <img class="profilePictures" src="' . $photos . '" alt="Profilová fotka">
                        <h1 class="nameInfo">' . htmlspecialchars($row['fname']) . ' ' . htmlspecialchars($row['lname']) . '</h1>
                        <p class="status">' . $statusInfo . '</p>
                        <p class="' . $messageClass . '">' .  htmlspecialchars($messageForUserShow)  . '</p>';

        if ($filePath) {
            if ($fileType == 'image') {
                $output .= '<div class="message-media message-image-container">
                                <img src="' . $filePath . '" alt="Image" data-lightbox="gallery" class="message-image">
                            </div>';
            } elseif ($fileType == 'video') {
                $output .= '<div class="message-media message-video-container">
                                <video controls>
                                    <source src="' . $filePath . '" type="video/mp4">
                                </video>
                            </div>';
            } elseif ($fileType == 'audio') {
                $output .= '<div class="message-media message-audio-container">
                                <audio controls>
                                    <source src="' . $filePath . '" type="audio/mp3">
                                </audio>
                            </div>';
            }
        }
        

        $output .= '</div>';
    }

    echo $output;
}
?>
