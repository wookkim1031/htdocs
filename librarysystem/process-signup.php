<?php
session_start();

$mysqli = require __DIR__ . "/database.php";  

$errors = [];

if(empty($_POST["name"])) { 
    $errors[] = "Name is required";
}

if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Valid email is required";
}

$emailDomain = explode("@", $_POST["email"])[1]; 

if ($emailDomain !== "ukaachen.de") {
    $errors[] = "Emails from ukaachen.de domain are only allowed";
}

if(! preg_match("/[a-z]/i", $_POST["password"])) {
    $errors[] = "Password must contain at least one letter";
}

if(! preg_match("/[0-9]/", $_POST["password"])) {
    $errors[] = "Password must contain at least one number";
}

if ($_POST["password"] !== $_POST["password_confirmation"]) {
    $errors[] = "Passwords must match";
}

if (!empty($errors)) {
    $_SESSION['signup_errors'] = $errors;
    header("Location: signup.php");
    exit;
}

$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);


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