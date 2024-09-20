<?php
include '../../db/db.php';

// Obtener el ID de la aplicación desde la solicitud
$aplicacion_id = isset($_GET['aplicacion_id']) ? intval($_GET['aplicacion_id']) : 0;

if ($aplicacion_id > 0) {
    // Obtener la prueba técnica de la base de datos
    $sql = "SELECT prueba_tecnica FROM aplicaciones WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $aplicacion_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($prueba_tecnica);
    $stmt->fetch();
    
    if ($prueba_tecnica) {
        // Configurar las cabeceras para la descarga del archivo
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="prueba_tecnica.pdf"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($prueba_tecnica));
        
        // Enviar el archivo
        echo $prueba_tecnica;
        exit;
    } else {
        echo "No se encontró la prueba técnica.";
    }
} else {
    echo "ID de aplicación inválido.";
}
?>
