<?php 
    include_once "config.php";
    require "../classes/User.php";
    require "../classes/Image.php";
    require_once "../classes/Database.php";

    $conn = (new Database())->connDB();

    $searchTerm = $_POST['searchTerm'];
    
    
    $searchTermResult = User::selectUser($conn, $searchTerm , $columns='*');

 
    $output= "";

    $count = count($searchTermResult);
    if($count >0){
        foreach($searchTermResult as $row){
            $photos = Image::getProfilePics($conn, $row['user_id']);


            include "data.php";
        }
    }else{
        $output .= '<p class="no-user">Žiadny používateľ vyhovujúci kritériám.</p>';
    }
    
    echo $output;