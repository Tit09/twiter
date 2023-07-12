<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "reseau_social";
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>

