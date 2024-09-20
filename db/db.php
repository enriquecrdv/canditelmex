<?php
$servername = "bguznt4pvaysivbniriy-mysql.services.clever-cloud.com";
$username = "u5f0ctjxoqziq3gc";  // Usuario de la base de datos
$password = "xVldCQ8whMPlp1wO9fuG";  // Contraseña de la base de datos
$dbname = "bguznt4pvaysivbniriy";  // Nombre de tu base de datos

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
