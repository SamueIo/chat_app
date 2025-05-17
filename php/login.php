<?php
session_start();

    require "../classes/User.php";
    include_once "config.php";

    $email = $_POST['email'];
    $password = $_POST['password'];

    if(!empty($email) && !empty($password)){
        if(User::authentication($conn, $email, $password)){
            $user_id = User::getUserId($conn, $email);
            $status= "Aktívny/a teraz";
            $_SESSION['user_id']= $user_id;
            echo "success";
        }else{
            echo "Nesprávne prihlasovacie údaje";
        }
    }else{
        echo 'Vyplnte všetky polia';
    }
?> 