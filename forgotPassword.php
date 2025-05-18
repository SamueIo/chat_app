<?php
require "./classes/User.php";
require "./classes/Database.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = (new Database())->connDB();

require 'vendor/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    $result = User::passwordReset($conn, $email);
    
    if (strpos($result, "E-mail nebol nájdený") !== false || strpos($result, "Nepodarilo sa uložiť token") !== false) {
        $message = $result ;
    } else {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = "************";
            $mail->SMTPAuth = //;
            $mail->Username = "****************";
            $mail->Password = "***************";
            $mail->SMTPSecure = "***";
            $mail->Port =//;

            $mail->setFrom('no-reply@tvojadomena.com', 'No Reply');
            $mail->addAddress($email);
            $mail->Subject = "Resetovanie hesla";
            $mail->Body = "Pre reset hesla kliknite na odkaz: " . $result;

            $mail->send();

            $message = 'Link odoslaný na email. Pokračujte cez email';

        } catch (Exception $e) {
            $message = $mail->ErrorInfo;
        }
    }
}




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/forgotPassword.css">
    <title>Document</title>
</head>
<body>
    <div class="container">
        <div class="messageInfo"><?php if(isset($message)){ echo $message; } ?></div>
        <form method="POST">
        <input type="email" name="email" placeholder="Zadaj svoj e-mail" required>
        <button type="submit">Obnoviť heslo</button>
        <a class="indexHref" href="./index.php">Späť na prihlásenie</a>
        </form>
    </div>

    
</body>
</html>
