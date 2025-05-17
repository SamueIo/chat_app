<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/index.css">
    <script src="https://kit.fontawesome.com/b517832f85.js" crossorigin="anonymous"></script>
    <title>Pigeon</title>
</head>
<body>
    <div id="wrapper">
        <section class="form signup">
            <header>Pigeon</header>
            <form action="#">
        <div class="error-txt"></div>
    
    <!-- Sekcia pre meno a priezvisko -->
            <div class="name-details" id="name-details">
                <div class="field input">
                    <label for="">First Name</label>
                    <input type="text" class="fname" name="fname" placeholder="First name"  disabled>
                </div>
                <div class="field input">
                    <label for="">Last Name</label>
                    <input type="text" class="lname" name="lname" placeholder="Last name" disabled>
                </div>
            </div>

            <!-- Sekcia pre email -->
            <div class="field input">
                <label for="">Email</label>
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <!-- Sekcia pre heslo -->
            <div class="field input">
                <label for="">Password</label>
                <input id="pswrd1" type="password" name="password" placeholder="Enter your password" required>
                <i class="fa-solid fa-eye"></i>
            </div>

            <!-- Sekcia pre druhé heslo (potvrdenie) -->
            <div class="field input" id="second-password">
                <label for="">Confirm Password</label>
                <input id="pswrd2" type="password" name="password_confirm" class="passwordControl" placeholder="Confirm your password" disabled>

            </div>

            <!-- Tlačidlo na odoslanie -->
            <div class="field button">
                <input id="submitBtn" type="submit" placeholder="Submit">
            </div>

            <!-- Odkazy na prepínanie medzi prihlásením a registráciou -->
            <div class="link" id="signin"><a href="#" id="toggleLink">Ste už zaregistrovaný? Prihláste sa</a> </div>
            <div class="link" id="registration" class="hidden"><a href="#" id="toggleLinkReg">Nemáte registráciu? Registrujte sa</a> </div>
            <div class="forgotPassword" id='toggleLinkPass' ><a href="./forgotPassword.php">Zabudli ste heslo? </a></div>
</form>


        </section>

    </div>
    


    <script src="./js/formular_change.js"></script>
    <script src="./js/signup_login.js"></script>
    <script src="./js/pswrd_show_hide.js"></script>
</body>
</html>