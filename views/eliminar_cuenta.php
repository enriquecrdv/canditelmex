<?php
session_start();
include '../db/db.php';

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'user')) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Preparar y ejecutar la consulta para eliminar la cuenta
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    
    if ($stmt->execute()) {
        // Cerrar sesión y redirigir al inicio de sesión
        session_unset();
        session_destroy();
        header("Location: ../login.php");
        exit();
    } else {
        $message = "Error al eliminar la cuenta: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Cuenta</title>
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
                <h5>Eliminar Cuenta</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($message)) { echo "<div class='alert alert-danger'>$message</div>"; } ?>
                
                <form method="POST" action="">
                    <p>Estás a punto de eliminar tu cuenta. Esta acción no se puede deshacer.</p>
                    <button type="submit" class="btn btn-danger w-100">Eliminar Cuenta</button>
                </form>
                
                <br>
                <a href="../index.php" class="btn btn-secondary w-100">Volver al Inicio</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
