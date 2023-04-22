<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$username = "root"; //only recommended when developing locally 
$password = "";
$dbname = "bib";

$mysqli = new mysqli(hostname: $host, 
                     username: $username, 
                     password: $password, 
                     database: $dbname);

if($mysqli -> connect_errno) {
    die("Connection error: " . $mysqli->connect_error);
}

return $mysqli; 

