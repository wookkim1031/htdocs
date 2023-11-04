<?php
    if (isset($_GET['error'])) {
        $error = $_GET['error'];
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <link rel="icon" href="/librarysystem/image/IMSA-LOGO.png">
    <link rel="stylesheet" type="text/css" href="style/signup.css">
</head>
<body>
    <div class="container">
        <h2> <img src="/librarysystem/image/IMSA-LOGO.png" alt="icon">  Bibilothek für <br> Medizinische Statistik</h2>

        <h1>Sign Up</h1>

        <div class="description-box">
            <p>This is the Anmeldung for Medizin Statistik library. In order to login, you need to provide a ukaachen.de email.</p>
        </div>

        <?php if (isset($error)): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form action="process-signup.php" method="post" novalidate>
            <div class="form-group">
                <label for="name"><img src="/librarysystem/image/user-solid.svg" alt="user">  USERNAME</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email"><img src="/librarysystem/image/envelope-regular.svg" alt="email"> EMAIL</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password"><img src="/librarysystem/image/key-solid.svg" alt="key"> PASSWORD</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="password_confirmation"><img src="/librarysystem/image/key-solid.svg" alt="key"> Confirm PASSWORD</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>
            <input type="hidden" name="default_role_id" value="1">
            <button type="submit">Sign Up</button>
        </form>

        <div class="form-footer">
            <a href="#" onclick="toggleContactBox()">Need Help?</a>
            <a href="login.php">Already have an account?</a>
        </div>

        <div id="contact-box" class="contact-box">
            <h3>Fragen Sie uns!</h3>
            <p>Sie haben Fragen rund um die Bibilothek oder Schwierigkeit über Signin? </p>
            <p> Schreiben Sie uns eine email. Wir antworten Ihnen schnell wie möglich.</p>
            <h3>Kontakt Information</h3>
            <h4>Bergrath, Arne</h4>
            <h4>Systemadministrator/in</h4>
            <p>email: abergrath@ukaachen.de</p>
        </div>
    </div>

    <script src="./js/signup.js"></script>
</body>
</html>
