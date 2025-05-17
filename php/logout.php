<?php

    require "../classes/User.php";
    require "../classes/Database.php";

    $conn = (new Database())->connDB();
    session_start();
    $user_id =$_SESSION['user_id'];

    $status = 'offline';
    User::updateUserStatus($conn, $user_id, $status);

    session_unset();
    session_destroy();

    header('Location: ../index.php');
    exit();
