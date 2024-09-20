<?php
include '../../db/db.php';

// Obtener el ID de la aplicación desde la solicitud
$aplicacion_id = isset($_GET['aplicacion_id']) ? intval($_GET['aplicacion_id']) : 0;

if ($aplicacion_id > 0) {
    // Obtener el CV de la base de datos
    $sql = "SELECT cv FROM aplicaciones WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $aplicacion_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($cv);
    $stmt->fetch();
    
    if ($cv) {
        // Configurar las cabeceras para la descarga del archivo
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="cv.pdf"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($cv));
        
        // Enviar el archivo
        echo $cv;
        exit;
    } else {
        echo "No se encontró el CV.";
    }
} else {
    echo "ID de aplicación inválido.";
}
?>
