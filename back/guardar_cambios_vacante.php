<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $departamento = $_POST['departamento'];
    $numero_id = $_POST['numero_id'];
    $fecha = $_POST['fecha'];
    $perfil = $_POST['perfil'];
    $descripcion = $_POST['descripcion'];
    $requisitos = $_POST['requisitos'];
    $foto = $_POST['foto'];

    // Actualizar los datos de la vacante
    $sql = "UPDATE vacantes SET 
                nombre='$nombre', 
                departamento='$departamento', 
                numero_id='$numero_id', 
                fecha='$fecha', 
                perfil='$perfil', 
                descripcion='$descripcion', 
                requisitos='$requisitos', 
                foto='$foto' 
            WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        // echo "Vacante actualizada exitosamente.";
    } else {
        echo "Error: " . $conn->error;
    }
}
header("Location: ../views/admin/lista_vacantes.php");
?>
