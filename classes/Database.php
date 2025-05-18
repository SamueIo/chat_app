<?php
class Database{
    public static function connDB(){
        //DB conn.....
        

        try{
            $conn = new PDO("mysql:host=$db_host;dbname=$db_name",
                            $db_user , $db_password);


             
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
    
        }catch (PDOException $e){
            echo "Connection failed:"  .$e->getMessage();
            exit;
        }
    }
    
    
}
