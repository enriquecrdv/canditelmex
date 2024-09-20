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
    $default_password = 'contrasena'; // Contraseña predeterminada

    // Preparar y ejecutar la consulta para actualizar la contraseña
    $sql = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $default_password, $user_id);
    
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <style>
        .card {
            width: 40%;
            margin: 0 auto;
            border-radius: 10px;
        }
        .center-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
    </style>
</head>
<body>
    <div class="center-container">
        <div class="card">
            <div class="card-header text-center">
                <h5>Restablecer Contraseña</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>
                
                <form method="POST" action="">
                    <p>Este formulario restablecerá tu contraseña a "contrasena".</p>
                    <button type="submit" class="btn btn-danger w-100">Restablecer Contraseña</button>
                </form>
                
                <br>
                <a href="../index.php" class="btn btn-secondary w-100">Volver al Inicio</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
