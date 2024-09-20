<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

include '../../db/db.php';

// Obtener el ID de la aplicación
$aplicacion_id = isset($_GET['aplicacion_id']) ? intval($_GET['aplicacion_id']) : 0;

// Obtener el CV de la aplicación
$sql = "SELECT cv, nombre FROM aplicaciones WHERE id='$aplicacion_id'";
$result = $conn->query($sql);
$aplicacion = $result->fetch_assoc();

if ($aplicacion) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . htmlspecialchars($aplicacion['nombre']) . '_CV.pdf"');
    echo $aplicacion['cv'];
} else {
    echo "No se encontró el CV.";
}
?>
