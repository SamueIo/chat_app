<?php 
require "./classes/User.php";
require "./classes/Image.php";
require "./classes/Database.php";

$conn = (new Database())->connDB();


    session_start();
    if(!isset($_SESSION['user_id'])){
        header("location: ./index.php");
    }
    $user_id = $_SESSION['user_id'];
    $user_info = User::getUserInfo($conn, $user_id, $columns = "*");
    $photos = Image::getProfilePics($conn, $user_id);
    $status = 'Aktívny/a teraz';
    $user_status = User::updateUserStatus($conn, $user_id, $status);

?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/user.css">
    <link rel="stylesheet" href="./css/photoView.css">
   
    <link rel="stylesheet" href="https://cdn.plyr.io/3.6.8/plyr.css" />
    <script src="https://cdn.plyr.io/3.6.8/plyr.polyfilled.js"></script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/b517832f85.js" crossorigin="anonymous"></script>


    <title>Pigeon</title>
</head>
<body>
    <div class="wrapper">
        <section class="user-interface">
            <header>
                

            </header>
                <div class="left-side">
                    <a href="./php/customProfile.php"><i class="fa-regular fa-user"></i></a>
                    <a href="./php/logout.php"><i id="log-out-icon" class="fa-solid fa-arrow-right-from-bracket"></i></a>
                <div id="myInfo" class="logged-user" logged-user-id="<?= $user_id ?>">
                    <img class="profilePictures" src="<?= htmlspecialchars($photos) ?>" alt="Profilová fotka">
                    <h1 id="myInfoName"><?= $user_info["fname"] ?> <?= $user_info["lname"]?></h1>
                    
                    
                </div>
               
                <div class="recent-messages">
                    <div class="searching-users">
                        <div class="searching-users-button">
                            <button><i class="fa-solid fa-magnifying-glass"></i></button>
                        </div>
                        <p id="firtNavTxt">&#8592; Najdi svoj prvy kontaktu tuto </p>
                        <div class="searching-users-input">
                            <input type="text">
                        </div>
                    </div>
                    <div class="users-list">
                        
                    </div>

                </div>

            </div>
            <div class="right-side">
                
                <div class="current-user">
                    <div class="photo-place">

                    </div>
                    <i id="go-back-arrow" class="fa-solid fa-arrow-left" aria-hidden="false"></i>
                </div>
                <div class="chat-box" id="chat-box">
                    
                    
                    
                   
                    <div class="users-messages">
                        
                        <div class="chat outgoing">
                            <div class="details">

                            </div>
                        </div>
                        <div class="chat incoming">
                            <div class="details">

                        </div>
                    </div>

                </div>
                <div class="typing-area-form">
                <form id="messageForm" class="typing-area" enctype="multipart/form-data">
                    <div class="replying-message"></div>
                    


                    <input type="text" id="message-input" name="message" placeholder="Nečum tak a piš" />
                   
                    <label for="file-input" class="file-upload">
                        <i class="fa-solid fa-photo-film"></i>
                    </label>
                    <span id="file-count"></span>
                    <input type="file" id="file-input" name="files[]" multiple style="display: none;" />

                    <button type="submit" class="send-message">
                        <i class="fa-regular fa-paper-plane"></i>
                    </button>
                    <button id="scrollToTopBtn" >
                        <i class="fa-solid fa-angles-down"></i>
                    </button>
                    
                </form>

                </div>

            </div>
                

        </section>
        
</div>

    </div>
    <div id="photoModal" class="modal">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <button id="prevBtn" class="nav-btn" onclick="changeImage(-1)">&#10094;</button> 
            <div class="modal-content">
                <img class="modal-img" id="modalImage">
            </div>
            <button id="nextBtn" class="nav-btn" onclick="changeImage(1)">&#10095;</button> 
            <div id="caption"></div>
            
        </div>
        
    
    <script src="js/jsFunctions.js"></script>

    <script src="js/users.js"></script>
    <script src="js/messages.js"></script>
    
    <script src="js/photoView.js"></script>
    <script src="js/scrollIntoView.js"></script>
    <script src="js/ui_toggle.js"></script>


</body>
</html>