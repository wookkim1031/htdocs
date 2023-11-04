<?php
session_start();

if (isset($_SESSION["user_id"])) {
    if (isset($_POST["new_name"]) || isset($_POST["new_telephone"])) {
        $mysqli = require __DIR__ . "/database.php";
        
        $user_id = $_SESSION["user_id"];
        $new_name = $_POST["new_name"] ?? null; 
        $new_telephone = $_POST["new_telephone"] ?? null; 

        
        $stmt = $mysqli->prepare("UPDATE users SET name = ?, telephone = ? WHERE id = ?");
        $stmt->bind_param("ssi", $new_name, $new_telephone, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    
    header("Location: user_dashboard.php");
} else {
    header("Location: login.php"); 
    exit();
}
?>
