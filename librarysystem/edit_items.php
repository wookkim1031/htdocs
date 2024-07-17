<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit;
}

$mysqli = require __DIR__ . "/database.php";

$response = ['success' => false, 'error' => '', 'message'=> ''];

if(isset($_POST['update_book'])) {
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $edition = $_POST['edition'];
    $location = $_POST['location'];
    $year = $_POST['year'];
    $publisher = $_POST['publisher'];
    $status = $_POST['status'];
    $isbn = $_POST['isbn'];

    //Fetch location_room name
    $location_stmt = $mysqli->prepare("SELECT room FROM location WHERE id = ?");
    $location_stmt->bind_param("i", $location);
    $location_stmt->execute();
    $location_stmt->bind_result($location_room);
    $location_stmt->fetch();
    $location_stmt->close();

     // Fetch the status name
     $status_stmt = $mysqli->prepare("SELECT status FROM status WHERE id = ?");
     $status_stmt->bind_param("i", $status);
     $status_stmt->execute();
     $status_stmt->bind_result($status_name);
     $status_stmt->fetch();
     $status_stmt->close();

    $stmt = $mysqli->prepare("UPDATE books SET title = ?, author = ?, location = ?, edition = ?, year = ?, status = ?, isbn = ?, publisher = ? WHERE id = ?");
    if ($stmt === false) {
        $error = $mysqli->error;
        header("Location: admin_searchbooks.php?error=SQL Error: " . urlencode($error));
        exit;
    }


    $stmt->bind_param("sssissssi", $title, $author, $location, $edition, $year, $status, $isbn, $publisher, $book_id);
    $stmt->execute();

    if($stmt->affected_rows > 0) {
        header("Location: admin_searchbooks.php?success=true&type=book&title=".urlencode($title)."&author=".urlencode($author)."&edition=".urlencode($edition)."&location=".urlencode($location)."&location_room=".urlencode($location_room)."&year=".$year."&status=".urlencode($status_name)."&publisher=".urlencode($publisher)."&isbn=".$isbn);
    } else {
        header("Location: admin_searchbooks.php?error=Keine Änderungen vorgenommen oder Fehler aufgetreten.");
    }
    $stmt->close();
} elseif (isset($_POST['update_magazine'])) {
    $magazine_id = $_POST['magazine_id'];
    $title = $_POST['title'];
    $jahrgang = $_POST['jahrgang'];
    $volumes = $_POST['volumes'];
    $standort = $_POST['standort'];

    $stmt = $mysqli->prepare("UPDATE magazines SET title = ?, jahrgang = ?, volumes = ?, standort = ? WHERE ID = ?");
    if ($stmt === false) {
        header("Location: admin_searchbooks.php?error=SQL Error.");
        exit;
    }

    $stmt->bind_param("ssssi", $title, $jahrgang, $volumes, $standort, $magazine_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: admin_searchbooks.php?success=true&type=magazine&title=".urlencode($title)."&jahrgang=".urlencode($jahrgang)."&volumes=".urlencode($volumes)."&standort=".urlencode($standort));
    } else {
        header("Location: admin_searchbooks.php?error=Keine Änderungen vorgenommen oder Fehler aufgetreten.");
    }
    $stmt->close();
} else {
    $response['error'] = 'Invalid request.';
}
exit;
?>
