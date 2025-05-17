<?php
class Messages {
    public static function getUserInfo($conn, $user_id, $columns="*") {
        $sql = "SELECT $columns FROM users WHERE user_id = :user_id";
    
        $stmt = $conn->prepare($sql);
    
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    
        try {
            if ($stmt->execute()) {
                $message = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $response = json_encode(["status" => "success", "message" => $message]);
                return $response;
            } else {
                $response = json_encode(['status' => 'error', 'message' => 'Chyba pri získavaní informácií']);
                return $response;
            }
    
        } catch (PDOException $e) {
            $response = json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            return $response;
        }
    }
    public static function generateChatId($user1_id, $user2_id) {
        return $user1_id < $user2_id ? $user1_id . $user2_id : $user2_id. $user1_id;
    }



    public static function addUserToChat($conn, $chat_id, $user_id) {

        $sql = "SELECT id FROM chats WHERE chat_id = :chat_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':chat_id', $chat_id);
        $stmt->execute();
    
        if ($stmt->rowCount() > 0) {

            $sql = "INSERT INTO chat_users (chat_id, user_id) VALUES (:chat_id, :user_id)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':chat_id', $chat_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
        } else {

            throw new Exception("Chat s týmto ID neexistuje.");
        }
    }


    
    
    public static function createChat($conn, $user1_id, $user2_id) {
        $conn->beginTransaction();
        
        $chat_id = self::generateChatId($user1_id, $user2_id);
    
        $sql = "SELECT chat_id FROM chats WHERE chat_id = :chat_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':chat_id', $chat_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $conn->commit();
            return $chat_id;

        }
    
        $sql = "INSERT INTO chats (chat_id, user1_id, user2_id, created_at) VALUES (:chat_id, :user1_id, :user2_id, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':chat_id', $chat_id);
        $stmt->bindParam(':user1_id', $user1_id);
        $stmt->bindParam(':user2_id', $user2_id);
        
        if ($stmt->execute()) {
            $conn->commit();
            return json_encode(['status' => 'success', 'chat_id' => $chat_id]);
        } else {
            $conn->rollBack();
            return json_encode(['status' => 'error', 'message' => 'Chyba pri vytváraní chatu']);
        }
    }
    

    public static function openChat($conn, $user1_id, $user2_id) {
        $conn->beginTransaction();
        $chat_id = self::generateChatId($user1_id, $user2_id);
        $chat_id = (int)$chat_id;

        $sql = "SELECT chat_id FROM chats WHERE chat_id = :chat_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":chat_id", $chat_id);
        $stmt->execute();
    
        if ($stmt->rowCount() > 0) {
            $messages = self::getMessages($conn, $chat_id);
            return json_encode(['status' => 'success', 'messages' => $messages]);

        }
    

        $sql = "INSERT INTO chats (chat_id, created_at) VALUES (:chat_id, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":chat_id", $chat_id);
    
        if ($stmt->execute()) {

            $messages = self::getMessages($conn, $chat_id);
            return json_encode(['status' => 'success', 'messages' => $messages]);

        } else {

            return json_encode(['status' => 'error', 'message' => 'Chyba pri vytváraní chatu']);
        }
    }
    public static function getMessages($conn, $chat_id) {
        try {
            $sql = "SELECT m.id, m.message, m.user_id, m.created_at,m.parent_message_id, u.fname, u.lname
                    FROM messages m
                    JOIN users u ON m.user_id = u.user_id
                    WHERE m.chat_id = :chat_id
                    ORDER BY m.created_at ASC";
        
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':chat_id', $chat_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Pre každú správu pridáme médiá, ak nejaké existujú
            foreach ($messages as &$message) {
                // Získanie médií pre každú správu
                $mediaSql = "SELECT file_url, file_type FROM media WHERE message_id = :message_id";
                $mediaStmt = $conn->prepare($mediaSql);
                $mediaStmt->bindParam(':message_id', $message['id'], PDO::PARAM_INT);
                $mediaStmt->execute();
                
                $media = $mediaStmt->fetchAll(PDO::FETCH_ASSOC);
                if ($media) {
                    $message['media'] = $media; 
                }
            }
            
            return json_encode($messages);
    
        } catch (PDOException $e) {
            return json_encode(['status' => 'error', 'message' => 'Chyba pri getMessages']);
        }
    }
    

    
    public static function getLatestSingleMessage($conn, $chat_id) {
        try {
            $sql = "SELECT m.id, m.message, m.user_id, m.created_at, m.read_at, u.fname, u.lname
                    FROM messages m
                    JOIN users u ON m.user_id = u.user_id
                    WHERE m.chat_id = :chat_id
                    ORDER BY m.created_at DESC LIMIT 1"; 
    
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':chat_id', $chat_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $message = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($message) {
                return $message;
            } else {
                return null; 
            }
    
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Chyba pri získavaní správ: ' . $e->getMessage()];
        }
    }
    
    
    

    public static function getLatestMessages($conn, $chat_id, $id) {
        try {
            $sql = "SELECT m.id, m.message, m.user_id, m.created_at, m.parent_message_id,
                            me.file_url, me.file_type, me.file_size
                    FROM messages m
                    LEFT JOIN media me ON m.id = me.message_id
                    WHERE m.chat_id = :chat_id 
                    AND m.id > :id
                    ORDER BY m.created_at ASC";
        
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':chat_id', $chat_id, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        
            // Získanie správ a médií
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            if (count($messages) > 0) {
                return json_encode([
                    'status' => 'success',
                    'messages' => $messages 
                ]);
            } else {
                return json_encode([
                    'status' => 'error',
                    'id' => $id,
                    'chat_id' => $chat_id,
                    'message' => 'no new messages.'
                ]);
            }
        } catch (PDOException $e) {
            return json_encode([
                'status' => 'error',
                'id' => $id,
                'chat_id' => $chat_id,
                'message' => 'Chyba pri získavaní správ: ' . $e->getMessage()
            ]);
        }
    }
    
    
    

    public static function sendMessage($conn, $chat_id, $user_id, $message, $parent_message_id = NULL, $files = []) {
        try {
            $sql = "SELECT 1 FROM chats WHERE chat_id = :chat_id LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':chat_id', $chat_id, PDO::PARAM_INT);
            $stmt->execute();
    
            if ($stmt->rowCount() == 0) {
                $response = json_encode(['status' => 'error', 'message' => 'Chat s týmto chat_id neexistuje']);
                error_log("Chyba: Chat neexistuje: " . $response);
                return $response;
            }
    
            if (empty($chat_id) || empty($user_id) || (empty($message) && empty($files))) {
                $response = json_encode(['status' => 'error', 'message' => 'Chýbajúce údaje alebo súbory']);
                error_log("Chýbajúce údaje alebo súbory: " . $response);
                return $response;
            }
    
            $sql = "INSERT INTO messages (chat_id, user_id, message, parent_message_id, created_at) 
                    VALUES (:chat_id, :user_id, :message, :parent_message_id, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':chat_id', $chat_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':message', $message);
            $stmt->bindParam(':parent_message_id', $parent_message_id, PDO::PARAM_INT);
    
            if ($stmt->execute()) {
                $message_id = $conn->lastInsertId();  

                if (!empty($files)) {
                    foreach ($files['name'] as $key => $file_name) {
                        // Získajte informácie o súbore
                        $file_tmp = $files['tmp_name'][$key];
                        $file_size = $files['size'][$key];
                        $file_type = mime_content_type($file_tmp);  

                        $upload_dir = '../chat_files/';
    
                        $file_url = 'chat_files/' . basename($file_name); 
                        $way_for_uploads = $upload_dir . basename($file_name); 
    
                        if (move_uploaded_file($file_tmp, $way_for_uploads)) {
                            $sql = "INSERT INTO media (message_id, user_id, file_url, file_type, file_size, created_at)
                                    VALUES (:message_id, :user_id, :file_url, :file_type, :file_size, NOW())";
                            $stmt = $conn->prepare($sql);
                            $stmt->bindParam(':message_id', $message_id);
                            $stmt->bindParam(':user_id', $user_id);
                            $stmt->bindParam(':file_url', $file_url);
                            $stmt->bindParam(':file_type', $file_type);
                            $stmt->bindParam(':file_size', $file_size);
    
                            if (!$stmt->execute()) {
                                $response = json_encode(['status' => 'error', 'message' => 'Chyba pri ukladaní súboru']);
                                error_log("Chyba pri ukladaní súboru: " . $response);
                                return $response;
                            }
                        } else {
                            $response = json_encode(['status' => 'error', 'message' => 'Chyba pri presune súboru']);
                            error_log("Chyba pri presune súboru: " . $response);
                            return $response;
                        }
                    }
                }
    
                $response = json_encode(['status' => 'success', 'message' => 'Správa s médiom bola odoslaná']);
                error_log("Správa s médiom odoslaná: " . $response);
                return $response;
            } else {
                $response = json_encode(['status' => 'error', 'message' => 'Chyba pri odosielaní správy']);
                error_log("Chyba pri odosielaní správy: " . $response);
                return $response;
            }
        } catch (PDOException $e) {
            $errorMessage = 'Chyba databázy: ' . $e->getMessage();
            $response = json_encode(['status' => 'error', 'message' => $errorMessage]);
            error_log("Chyba pri databázovej operácii: " . $response);
            return $response;
        }
    }
    


    public static function markMessageAsRead($conn, $messageId, $userId){

        $sql = "UPDATE messages
                SET read_at = NOW()
                WHERE id = :message_id AND user_id != :user_id";


        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':message_id', $messageId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        } else {
            $errorInfo = $stmt->errorInfo();
            error_log("SQL Error: " . var_export($errorInfo, true), 3, "../logs/error_log.txt");
            return false;
        }
    }




}

?>
