<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit;
}

if(isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role_id = $_POST['role_id'];
    $password = $_POST['password'];
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $mysqli = require __DIR__ . "/database.php";

    $stmt = $mysqli->prepare("INSERT INTO users (name, email, role_id, password_hash) VALUES (?,?,?,?)");
    $stmt->bind_param("ssis", $name, $email, $role_id, $passwordHash);
    $stmt->execute();

    if($stmt->affected_rows >0 ){
        $_SESSION['message']= "User erfolgreich hinzugefügt";
    } else {
        $_SESSION['error'] ="Benutzer hinzufügen fehlgeschlafen.";
    }
    $stmt->close();
    header("Location: admin_users.php");
    exit;
}
?>