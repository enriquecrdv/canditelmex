<?php
$servername = "localhost";
$username = "u141673834_MV5C9";  // Usuario de la base de datos
$password = "Piloto.78";  // Contraseña de la base de datos
$dbname = "u141673834_m6l0Q";  // Nombre de tu base de datos

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
