<?php
$mysqli = require __DIR__ . "/database.php";
$token = $_GET['token'];

$sql = "UPDATE users SET verified = 1 WHERE token = ? AND verified = 0";
$stmt = $mysqli->$prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();

if ($stmt -> affected_rows == 1) {
    echo "Email verified successfully";
} else {
    echo "Invalid token or email already verified.";
}
?>
