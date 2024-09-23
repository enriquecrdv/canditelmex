<?php
session_start();
include '../db/db.php';

// Verifica si se ha proporcionado un ID de aplicación
if (isset($_GET['id'])) {
    $aplicacion_id = intval($_GET['id']);

    // Consulta para obtener el CV
    $sql = "SELECT prueba_tecnica, nombre FROM aplicaciones WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $aplicacion_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($cvContent, $nombre);
    
    if ($stmt->num_rows > 0) {
        $stmt->fetch();

        // Establecer encabezados para la descarga del archivo
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $nombre . '_Prueba_tecnica.pdf"');
        header('Content-Length: ' . strlen($cvContent));
        echo $cvContent;
    } else {
        echo "prueba no encontrado.";
    }
    $stmt->close();
} else {
    echo "ID de aplicación no especificado.";
}

// Cerrar conexión
$conn->close();
?>
