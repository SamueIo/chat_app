<?php
class Database{
    public static function connDB(){
        $db_host="sql201.infinityfree.com";
        $db_user="if0_38742290";
        $db_password="SxvxsAptlSQNEA";
        $db_name="if0_38742290_pigeon" ;
        // $db_host="localhost";
        // $db_user="samuello";
        // $db_password="e_shopadmin";
        // $db_name="chat";
        
        

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
