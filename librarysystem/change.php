<?php

$mysqli = require __DIR__ . "/database.php";
session_start();


?>

<h1>Admin Dashboard</h1>
<p>
            <?php
                echo "<p>Your role is: " . htmlspecialchars($userRole) . "</p>";
            
            ?>
        </p>