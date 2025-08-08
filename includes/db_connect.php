<?php
$servername = "145.223.104.13";
$username = "u599795228_user42";
$password = "6Bg225-1";
$dbname = "u599795228_db42";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>