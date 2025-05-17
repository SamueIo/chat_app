<?php 
require "../classes/User.php";
require "../classes/Image.php";
require "../classes/Database.php";

$conn = (new Database())->connDB();
session_start();

if(!isset($_SESSION['user_id'])){
    header("location: ./index.php");
}
$user_id = $_SESSION['user_id'];
$user_info = User::getUserInfo($conn, $user_id, $columns = "*");

$status = 'Aktívny/a teraz';
$user_status = User::updateUserStatus($conn, $user_id, $status);
$imagePic = Image::uploadProfilePicture($conn);

?>

<!DOCTYPE html>
<html lang="sk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../css/customProfile.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://kit.fontawesome.com/b517832f85.js" crossorigin="anonymous"></script>
<title>Pigeon</title>
</head>
<body>
<div class="wrapper">
    <section class="user-interface">
        <header>
            

        </header>
        <form action="#" method="POST" enctype="multipart/form-data">
           <label for="profilePhoto">Vyberte profilovú fotku:</label>
           <input type="file" name="profilePhoto" id="profilePhoto" accept="image/*">
           <button type="submit">Nahrať fotku</button>
           <p class="ppInfo"><?=$imagePic?></p>
        </form>
        <a href="../user.php">spat</a>
            

    </section>

</div>

<script src="./js/jsFunctions.js"></script>

<script src="./js/users.js"></script>
<script src="./js/messages.js"></script>


</body>
</html>