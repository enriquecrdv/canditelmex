<?php
$servername = "localhost";
$username = "root";  // Usuario de la base de datos
$password = "";  // Contraseña de la base de datos
$dbname = "bguznt4pvaysivbniriy";  // Nombre de tu base de datos

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
