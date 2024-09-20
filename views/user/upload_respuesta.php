<?php
session_start();
if ($_SESSION['role'] != 'user') {
    header("Location: ../../login.php");
    exit;
}

include '../../db/db.php';

// Verificar si se ha enviado un archivo y una solicitud POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['respuesta_prueba_tecnica']) && isset($_POST['aplicacion_id'])) {
    $aplicacion_id = intval($_POST['aplicacion_id']);
    
    if ($aplicacion_id > 0) {
        $fileTmpPath = $_FILES['respuesta_prueba_tecnica']['tmp_name'];
        $fileName = $_FILES['respuesta_prueba_tecnica']['name'];
        $fileSize = $_FILES['respuesta_prueba_tecnica']['size'];
        $fileType = $_FILES['respuesta_prueba_tecnica']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        // Define el nuevo nombre del archivo para evitar colisiones
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        
        // Directorio donde se guardarán los archivos
        $uploadFileDir = '../../uploads/';
        $dest_path = $uploadFileDir . $newFileName;
        
        // Mover el archivo al directorio de subidas
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // Guardar la ruta del archivo en la base de datos
            $sql_update = "UPDATE aplicaciones SET prueba_tecnica = ? WHERE id = ?";
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param('si', $dest_path, $aplicacion_id);
            if ($stmt->execute()) {
                $message = "Respuesta de la prueba técnica subida exitosamente.";
            } else {
                $message = "Error al guardar la ruta de la respuesta de la prueba técnica en la base de datos: " . $conn->error;
            }
        } else {
            $message = "Error al mover el archivo al directorio de subidas.";
        }
    } else {
        $message = "ID de aplicación inválido.";
    }
}
?>

<!-- Mostrar mensaje de éxito o error -->
<div class="alert alert-info"><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></div>
