<?php

if(empty($_POST["name"])) { // if name is empty
    die("Name is required");
}

if(! filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    die("Validate email is required");
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

$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

$mysqli = require __DIR__ . "/database.php"; //to get the directory of the current file 

$sql = "INSERT INTO users(name, email, password_hash)
        VALUES(?,?,?)";
        
$stmt = $mysqli->stmt_init();

if(!$stmt->prepare($sql)) {
    die("SQL error: " . $mysqli ->error);
};

$stmt->bind_param("sss",
                  $_POST["name"],
                  $_POST["email"],
                  $password_hash);

if($stmt->execute()) {
    
    header("Location: signup-success.html");
    exit;
} else {
    die($mysqli->error . " ". $mysqli->errno);
}
