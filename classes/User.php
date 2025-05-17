<?php


class User {

    public static function emailCheck($conn, $email) {
        $sql = "SELECT email FROM users WHERE email = :email";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);

        try {
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    throw new Exception("Email už existuje!");
                } else {
                    return true;
                }
            }
        } catch (Exception $e) {

            echo $e->getMessage();
            return false;
        }
    }

    public static function registrationUser($conn, $fname, $lname, $email, $password, $status) {
        $sql = "INSERT INTO users (fname, lname, email, password, status) 
                VALUES (:fname, :lname, :email, :password, :status)";
    
        $stmt = $conn->prepare($sql);
    
      
        $stmt->bindValue(":fname", $fname, PDO::PARAM_STR);
        $stmt->bindValue(":lname", $lname, PDO::PARAM_STR);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
    
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bindValue(":password", $hashedPassword, PDO::PARAM_STR);
        $stmt->bindValue(":status", $status, PDO::PARAM_STR);
    
        try {

            if ($stmt->execute()) {

                $id = $conn->lastInsertId();
                return $id;
            } else {
                throw new Exception("Vytvorenie uživateľa zlyhalo");
            }
        } catch (Exception $e) {

            echo "Typ chyby: " . $e->getMessage();
        }
    }


    public static function getUserId($connection, $email){
        $sql = "SELECT user_id
                FROM users
                WHERE email= :email";
    
        $stmt = $connection->prepare($sql);
        
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);

        try{
            if($stmt->execute()){
                $result = $stmt->fetch();
                $user_id = $result[0];
                return $user_id;
            }else{
                throw new Exception ("Ziskanie id uživateľa zlihalo");
            }
        }catch(Exception $e){
            echo ("Typ chyby: ".$e->getMessage());
        }
    }

    public static function authentication($conn, $email, $password){
        $sql = "SELECT password 
                FROM users
                WHERE email = :email" ;
    
        $stmt = $conn->prepare($sql);

        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        
        
        try{
            if($stmt->execute()){
                if($user = $stmt->fetch()){
                    return password_verify($password, $user[0]);
                }
            }else{
                throw new Exception("Overenie zlihalo");
            }
        }catch(Exception $e){
            error_log("Chyba pri fukncii authentication\n", 3,"../errors/error.log");
            echo ("Typ chyby: " .$e->getMessage());
        }
        
    }
    public static function getJSONUserInfo($conn, $user_id, $columns="*") {
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

    public static function getUserInfo($conn, $user_id, $columns = "*") {
        $sql = "SELECT $columns
                FROM users
                WHERE user_id = :user_id";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
    
        try {
            if ($stmt->execute()) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result === false) {
                    return null;
                }
                return $result;
            } else {
                throw new Exception("Získanie id užívateľa zlyhalo");
            }
        } catch (Exception $e) {
            $logFile = __DIR__ . '/logs/error_log.txt'; 
            $errorMessage = date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n";
            
            error_log($errorMessage, 3, $logFile);

    
            return null;
        }
    }

    public static function getEveryUserInfo($conn, $columns = "*") {
        $user_id = $_SESSION['user_id'] ;
        $sql = "SELECT 
                    u.$columns,
                    m.message AS last_message,
                    m.created_at AS last_message_time,
                    c.chat_id
                FROM 
                    chats c
                JOIN 
                    users u ON u.user_id IN (c.user1_id, c.user2_id)
                LEFT JOIN 
                    messages m ON m.chat_id = c.chat_id
                WHERE 
                    (c.user1_id = :user_id OR c.user2_id = :user_id) 
                    AND u.user_id != :user_id 
                ORDER BY 
                    (m.created_at IS NOT NULL) DESC, m.created_at DESC";
                        
                        $stmt = $conn->prepare($sql);
        $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);

    
        try {
            if ($stmt->execute()) {
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($result === false) {
                    return null;
                }
                return $result;
            } else {
                throw new Exception("Získanie id užívateľa zlyhalo");
            }
        } catch (Exception $e) {
            $logFile ='../logs/error_log.txt'; 
            $errorMessage = date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n";
            
            error_log($errorMessage, 3, $logFile);

    
            return null;
        }
    }

    public static function selectUser($conn, $searchTerm , $columns = "*") {
        $sql = "SELECT $columns
                FROM users
                WHERE fname LIKE :searchTerm OR lname LIKE :searchTerm";
        
        $stmt = $conn->prepare($sql);

        $searchTerm = "%$searchTerm%";

        $stmt->bindValue(":searchTerm", $searchTerm, PDO::PARAM_STR);
    
        try {
            if ($stmt->execute()) {
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($result === false) {
                    return null;
                }
                return $result;
            } else {
                throw new Exception("Hladanie užívateľa zlyhalo");
            }
        } catch (Exception $e) {
            $logFile = __DIR__ . '/logs/error_log.txt'; 
            $errorMessage = date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n";
            
            error_log($errorMessage, 3, $logFile);

    
            return null;
        }
    }
    public static function updateLastActive($conn, $user_id) {

        $currentDateTime = date("Y-m-d H:i:s");

        $sql = "UPDATE users SET last_active = :currentDateTime 
                WHERE user_id = :user_id";

        $stmt = $conn->prepare($sql);


        $stmt->bindValue(":currentDateTime", $currentDateTime, PDO::PARAM_STR); 
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT); 
    
        try {
            if ($stmt->execute()) {
                return true; 
            } else {
                throw new Exception("Chyba v updateLastActive");
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            return false; 
        }
    }

    public static function updateUserStatus($conn, $user_id, $status) {

        $sql = "UPDATE users SET status = :status 
                WHERE user_id = :user_id";

        $stmt = $conn->prepare($sql);

        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT); 
        $stmt->bindValue(":status", $status, PDO::PARAM_STR); 
    
        try {
            if ($stmt->execute()) {
                return true; 
            } else {
                throw new Exception("Chyba v updateLastActive");
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            return false; 
        }
    }
    public static function passwordReset($conn, string $email): string {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();
    
        if (!$user) {
            return "E-mail nebol nájdený.";
        }
    
        $token = bin2hex(random_bytes(32));
        $created_at = date("Y-m-d H:i:s");
    
        $stmt = $conn->prepare("
            INSERT INTO password_resets (email, token, created_at)
            VALUES (:email, :token, :created_at)
        ");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->bindValue(':created_at', $created_at, PDO::PARAM_STR);
    
        if (!$stmt->execute()) {
            return "Nepodarilo sa uložiť token.";
        }
    
        $adress = "pigeon.kesug.com/admin/reset_password.php?token=$token";
        
        $reset_link = self::correctAddress($adress);
    
        return $reset_link;
    }
    
    public static function correctAddress($adress): string {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            return 'https://' . $adress;  
        } else {
            return 'http://' . $adress;  
        }
    }
    
    
}
    

    
    
    

    


