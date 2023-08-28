<?php

$error = null;

if(empty($_POST["name"])) { // if name is empty
    die("Name is required");
}

if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    die("Valid email is required");
}

$emailDomain = explode("@", $_POST["email"])[1]; // Extract the domain from the email address

if ($emailDomain !== "ukaachen.de") {
    $error = "Emails from ukaachen.de domain are only allowed";
}

if(! preg_match("/[a-z]/i", $_POST["password"])) {
    die("Password must contain at least one letter");
}

if(! preg_match("/[0-9]/", $_POST["password"])) {
    die("Password must contain at least one letter");
}

if ($_POST["password"] !== $_POST["password_confirmation"]) {
    die("Passwords must match");
}

if ($error) {
    header("Location: signup.php?error=" . urlencode($error));
    exit;
}

$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

$mysqli = require __DIR__ . "/database.php"; //to get the directory of the current file 

$defaultRoleId = 1;

$sql = "INSERT INTO users(name, email, password_hash, role_id)
        VALUES(?,?,?,?)";
        
$stmt = $mysqli->stmt_init();

if(!$stmt->prepare($sql)) {
    die("SQL error: " . $mysqli ->error);
};

$stmt->bind_param("sssi",
                  $_POST["name"],
                  $_POST["email"],
                  $password_hash,
                  $defaultRoleId);

if($stmt->execute()) {
    
    header("Location: index.php");
    exit;
} else {
    die($mysqli->error . " ". $mysqli->errno);
}

?>