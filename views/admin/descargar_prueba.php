<?php
include '../../db/db.php';

// Verificar si se ha recibido el ID de la aplicación
if (isset($_GET['aplicacion_id'])) {
    $aplicacion_id = intval($_GET['aplicacion_id']);

    // Consultar la prueba técnica de la aplicación en la base de datos
    $sql = "SELECT prueba_tecnica, nombre FROM aplicaciones WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $aplicacion_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($prueba_tecnica, $nombre);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();

        // Verificar si hay una prueba técnica disponible
        if ($prueba_tecnica) {
            // Enviar los encabezados para la descarga
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="prueba_tecnica_' . $aplicacion_id . '.pdf"');
            header('Content-Length: ' . strlen($prueba_tecnica));

            // Enviar el contenido del archivo
            echo $prueba_tecnica;
        } else {
            echo "No hay prueba técnica disponible para esta aplicación.";
        }
    } else {
        echo "Aplicación no encontrada.";
    }
} else {
    echo "ID de aplicación no especificado.";
}
?>
