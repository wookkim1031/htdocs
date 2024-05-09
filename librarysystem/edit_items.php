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
    $year = $_POST['year'];
    $publisher = $_POST['publisher'];
    $isbn = $_POST['isbn'];

    $stmt = $mysqli->prepare("UPDATE books SET title = ?, author = ?,edition =?, year = ?, isbn=?, publisher = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $title, $author, $edition ,$year, $isbn, $publisher, $book_id);
    $stmt->execute();

    if($stmt->affected_rows > 0) {
        header("Location: admin_searchbooks.php?success=true&type=book&title=".urlencode($title)."&author=".urlencode($author)."&edition=".urlencode($edition)."&year=".$year."&publisher=".urlencode($publisher)."&isbn=".$isbn);

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
        $stmt->bind_param("ssssi", $title, $jahrgang, $volumes, $standort, $magazine_id);
        $stmt->execute();
    
        if ($stmt->affected_rows > 0) {
            header("Location: admin_searchbooks.php?success=true&type=magazine&title=".urlencode($title)."&jahrgang=".urlencode($jahrgang)."&volumes=".urlencode($volumes)."&standort=".$standort);
        } else {
            header("Location: admin_searchbooks.php?error=Keine Änderungen vorgenommen oder Fehler aufgetreten.");
        }
        $stmt->close();
}else {
    $response['error'] = 'Invalid request.';
}
exit;

?>