<?php
session_start();
require "../classes/User.php";
include_once "config.php";

$fname = $_POST['fname'];
$lname = $_POST['lname'];
$email = $_POST['email'];
$password = $_POST['password'];
$password_confirm = $_POST['password_confirm']; 

if (!empty($fname) && !empty($lname) && !empty($email) && !empty($password) && !empty($password_confirm)) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        
        if (User::emailCheck($conn, $email)) {
            
            if (strlen($password) < 6) {
                echo "Heslo musí mať aspoň 6 znakov!";
            } 
            elseif ($password !== $password_confirm) {
                echo "Heslá sa nezhodujú!";
            } else {
                $status = "Aktívny/a teraz";
                if (User::registrationUser($conn, $fname, $lname, $email, $password, $status)) {

                    $user_id = User::getUserId($conn, $email);
                    $_SESSION['user_id'] = $user_id;
                    echo "success"; 
                } else {
                    echo "Niečo sa pokazilo pri registrácii!";
                }
            }
        } else {
            echo "$email - Tento email je už zaregistrovaný!";
        }
    } else {
        echo "$email - Nesprávny formát emailu!";
    }
} else {
    echo 'Vyplňte všetky polia!';
}
?>
