<?php
session_start();

if (isset($_SESSION["role_id"]) && $_SESSION["role_id"] == 2) {
    $mysqli = require __DIR__ . "/database.php";
    
    $user_id = $_SESSION["user_id"]; 

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    header('Location: index.php');
    exit;
}

?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    Admin page
</body>
</html>