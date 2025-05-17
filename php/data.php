<?php
$output .= '<div class="logged-user user" data-user-id="' . $row['user_id'] . '">
                <img class="profilePictures" src="' . $photos . '" alt="Profilová fotka">
                <h1 class="nameInfo">' . $row['fname'] . ' ' . $row['lname'] . '</h1>
                
            </div>';