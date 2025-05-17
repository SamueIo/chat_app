<?php

class Image {
    public static function uploadProfilePicture($conn) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profilePhoto'])) {
            $file = $_FILES['profilePhoto'];
            $fileName = basename($file['name']);
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileError = $file['error'];
            $fileType = $file['type'];
    
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
            if (in_array($fileExt, $allowed)) {
                if ($fileError === 0) {
                    if ($fileSize < 5000000) {
                        $uploadDir = '../uploads/profile_pics/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true); 
                        }
    
                        $userId = $_SESSION['user_id'];
    
                        // Zisti existujúcu profilovú fotku
                        $stmtSelect = $conn->prepare("SELECT profile_pic FROM users WHERE user_id = :user_id");
                        $stmtSelect->bindParam(':user_id', $userId, PDO::PARAM_INT);
                        $stmtSelect->execute();
                        $existingPhoto = $stmtSelect->fetchColumn();
    
                        // Vymaže starú fotku (okrem defaultnej)
                        if ($existingPhoto && $existingPhoto !== 'default.jpg') {
                            $existingPath = $uploadDir . $existingPhoto;
                            if (file_exists($existingPath)) {
                                unlink($existingPath);
                            }
                        }
    
                        // Názov novej fotky
                        $newFileName = 'user_' . $userId . '.' . $fileExt;
                        $uploadFilePath = $uploadDir . $newFileName;
    
                        if (move_uploaded_file($fileTmpName, $uploadFilePath)) {
                            $stmt = $conn->prepare("UPDATE users SET profile_pic = :profile_pic WHERE user_id = :user_id");
                            $stmt->bindParam(':profile_pic', $newFileName, PDO::PARAM_STR);
                            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    
                            if ($stmt->execute()) {
                                return "Fotka bola úspešne nahraná!";
                            } else {
                                return "Chyba pri aktualizácii databázy!";
                            }
                        } else {
                            return "Chyba pri nahrávaní fotky!";
                        }
                    } else {
                        return "Súbor je príliš veľký!";
                    }
                } else {
                    return "Došlo k chybe pri nahrávaní súboru!";
                }
            } else {
                return "Nepodporovaný formát súboru!";
            }
        }
    }
    
    
    public static function getProfilePics($conn, $user_id) {
        $sql = "SELECT profile_pic FROM users WHERE user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    
        $defaultPic = 'uploads/profile_pics/default.png';
    
        $stmt->bindColumn(1, $profilePic);
        $stmt->fetch();
    
        return $profilePic ? 'uploads/profile_pics/' . $profilePic : $defaultPic;
    } 

}