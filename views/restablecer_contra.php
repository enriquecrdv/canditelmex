<?php
session_start();
include '../db/db.php';

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'user')) {
    header("Location: ../login.php");
    exit;
}

// Variable para mensajes de estado
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['id'];
    $new_password = $_POST['new_password']; // Obtener la nueva contraseña

    // Preparar y ejecutar la consulta para actualizar la contraseña
    $sql = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $new_password, $user_id);
    
    if ($stmt->execute()) {
        $message = "Contraseña restablecida con éxito.";
    } else {
        $message = "Error al restablecer la contraseña: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer Contraseña</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/restablecer_contra.css">
</head>
<body>

<div class="main-content bootstrap-encapsulated">
    <div class="container">
        <div class="center-container">
            <div class="card shadow-lg">
                <div class="card-header text-center">
                    <h5>Restablecer Contraseña</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nueva Contraseña</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Restablecer Contraseña</button>
                    </form>
                    
                    <br>
                    <a href="../index.php" class="btn btn-secondary w-100">Volver al Inicio</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Encapsular Bootstrap para no interferir -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

