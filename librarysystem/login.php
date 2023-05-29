<?php

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $mysqli = require __DIR__ . "/database.php";

    $sql = sprintf("SELECT * FROM users 
            WHERE email = '%s'",
            $mysqli->real_escape_string($_POST["email"])); //sql injection protection
    $result = $mysqli->query($sql);

    $user = $result->fetch_assoc();

    if($user) {

        if(password_verify($_POST["password"], $user["password_hash"])) {

            session_start();

            session_regenerate_id();
            
            $_SESSION["user_id"] = $user["id"];

            header("Location: ../librarysystem");
            exit;
        }
    }

    $is_invalid = true;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title> Login </title>
    <link rel="icon" href="/librarysystem/image/IMSA-LOGO.png">
    <link rel="stylesheet" type="text/css" href="style/login.css">

</head>
<body>
    <div class="login-container">
        <h1> <img src="/librarysystem/image/IMSA-LOGO.png" alt="icon"></h1>
        <h2>Login Medizin Statistik</h2>
        <form method="post" class="login-form">
            <input type="email" name="email" id="email" 
               value="<?= htmlspecialchars($_POST["email"] ?? "")   ?>" required>
            <input type="password" name="password" id= "password" placeholder="Password" required>
                <!-- error message -->
                <?php if ($is_invalid) : ?>
                    <em>Invalid login</em>
                <?php endif; ?>
            <input type="submit" value="Login">
        </form>

        <div class="form-footer">
            <a href="#">Forgot password?</a>
            <a href="signup.php">Sign Up</a>
        </div>
    </div>
</body>
</html>