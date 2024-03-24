<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit;
}

$mysqli = require __DIR__ . "/database.php";

$response = ['success' => false, 'error' => ''];

if(isset($_POST['update_book'])) {
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $year = $_POST['year'];
    $publisher = $_POST['publisher'];

    $stmt = $mysqli->prepare("UPDATE books SET title = ?, author = ?, year = ?, publisher = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $title, $author, $year, $publisher, $book_id);
    $stmt->execute();

    if($stmt->affected_rows > 0) {
        $response['success'] = true;
    } else {
        $response['error'] = 'No changes made or error occurred.';
    }
    $stmt->close();
} elseif (isset($_POST['update_magazine'])) {
        $magazine_id = $_POST['magazine_id'];
        $title = $_POST['title'];
        $jahrgang = $_POST['jahrgang'];
        $volumes = $_POST['volumes'];
        $standort = $_POST['standort'];
    
        $stmt = $mysqli->prepare("UPDATE magazines SET title = ?, jahrgang = ?, volumes = ?, standort = ? WHERE ID = ?");
        $stmt->bind_param("ssssi", $title, $jahrgang, $volumes, $standort, $magazine_id);
        $stmt->execute();
    
        if ($stmt->affected_rows > 0) {
            $response['success'] = true;
        } else {
            $response['error'] = 'No changes made or error occurred.';
        }
        $stmt->close();
}else {
    $response['error'] = 'Invalid request.';
}

echo json_encode($response);

?>