<?php 

session_start();

if (isset($_SESSION["user_id"])) { //check for the user_id
    
    $mysqli = require __DIR__ . "/database.php"; //get the databsae to get the connection

    $sql = "SELECT * FROM users
            WHERE  id = {$_SESSION["user_id"]}";
    
    $result = $mysqli->query($sql);

    $user = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title> Home</title>
    <link rel="stylesheet" type="text/css" href="style/index.css">
    <link rel="icon" href="/librarysystem/image/IMSA-LOGO.png">
</head>
<body>
    <?php if(isset($user)): ?>
        <?php include 'navbar.php' ?>
        <?php include 'searchbooks_index.php' ?>
        <div class="introduction">
            <h2>Bibilothek von Institut für Medizinische Statistik</h2>
                <p>Die Medizinische Statistik-Bibliotheken haben eine neue Suchplattform eingerichtet - Search Our Collections. Mit diesem Tool können Sie unsere Print- und Online-Sammlungen - vor allem Bücher, Zeitschriften und Artikel - durchsuchen, anfordern und darauf zugreifen. Im Folgenden finden Sie weitere Informationen, die Ihnen helfen, die gewünschten Materialien zu finden und zu erhalten.</p>
            <h3>Default Suche </h3>
                <p>Die Default-Suche befindet sich unter der Navigationsleiste auf der Startseite. Diese bietet eine schnelle Suche nach allen Magazinen und Büchern. </p>
            <h3>Buchsuche </h3>
                <p>Eine filterbare und durchsuchbare Liste von Datenbanken und anderen Online-Ressourcen. Die Bücher sind in Seiten unterteilt und mit Hilfe des Alphabets A-Z können Sie die Bücher bequem finden.</p>
            <h3>Magazine Suche </h3>
                <p>A filterable and searchable list of databases and other online resources. The Magazines are divided in pages and using alphabet A-Z, you can find the books convinently.</p>
        </div>
        <?php include 'footnotes.php' ?>
    <?php else: ?>
        
        <?php include 'login.php' ?>
    
    <?php endif; ?>
</body>
</html>