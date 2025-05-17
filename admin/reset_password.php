<?php
require "../classes/Database.php";
require "../classes/User.php";  

$conn = (new Database())->connDB();

$message = ''; // Zobrazenie odpovede

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = :token AND created_at > NOW() - INTERVAL 1 HOUR");
    $stmt->bindValue(':token', $token, PDO::PARAM_STR);
    $stmt->execute();
    $resetRequest = $stmt->fetch();

    if (!$resetRequest) {
        $message = "Token je neplatný alebo vypršal.";
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword !== $confirmPassword) {
            $message = "Heslá sa nezhodujú.";
        } elseif (strlen($newPassword) < 6) {
            $message = "Heslo musí mať aspoň 6 znakov.";
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            $stmt = $conn->prepare("UPDATE users SET password = :password WHERE email = :email");
            $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindValue(':email', $resetRequest['email'], PDO::PARAM_STR);

            if ($stmt->execute()) {
                $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = :token");
                $stmt->bindValue(':token', $token, PDO::PARAM_STR);
                $stmt->execute();

                $message = "Heslo bolo úspešne resetované.";
            } else {
                $message = "Chyba pri zmene hesla. Skúste to znova.";
            }
        }
    }
} else {
    $message = "Token nebol poskytnutý.";
}
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resetovanie hesla</title>
    <link rel="stylesheet" href="../css/reset_password.css">
</head>
<body>
    <form method="POST" action="">
        <h2>Zadajte nové heslo</h2>

        <?php if (!empty($message)): ?>
            <div class="form-message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <input type="password" id="new_password" name="new_password" placeholder="Nové heslo" required><br><br>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Potvrdiť nové heslo" required><br><br>
        <input type="submit" value="Resetovať heslo">
        <a class="indexHref" href="../index.php">Späť na prihlásenie</a>
    </form>
</body>
</html>
