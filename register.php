<?php
session_start();
include 'db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; // Contraseña sin encriptar
    $nombre = $_POST['nombre'];
    $email = $_POST['email']; // Nuevo campo para el correo electrónico
    
    // Verificar si el nombre de usuario ya existe
    $check_sql = "SELECT * FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('s', $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $message = "El nombre de usuario ya existe. Elige otro.";
    } else {
        // Insertar nuevo usuario con rol 'user' por defecto y sin encriptar la contraseña
        $sql = "INSERT INTO users (username, password, role, nombre, email) 
                VALUES (?, ?, 'user', ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssss', $username, $password, $nombre, $email); // Incluir el email
        
        if ($stmt->execute()) {
            $message = "Registro exitoso. Ahora puedes iniciar sesión.";
        } else {
            $message = "Error al registrar: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css">
    <style>
        /* Estilos para centrar la tarjeta */
        body, html {
            height: 100%;
        }

        .card {
            width: 40%;
            margin: auto;
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
                <h5>Registro de Usuario</h5>
            </div>
            <div class="card-body">
                <?php if (isset($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username">Nombre de Usuario:</label>
                        <input type="text" name="username" required class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password">Contraseña:</label>
                        <input type="password" name="password" required class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="nombre">Nombre Completo:</label>
                        <input type="text" name="nombre" required class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="email">Correo Electrónico:</label>
                        <input type="email" name="email" required class="form-control">
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Registrar</button>
                </form>

                <br>
                <a href="login.php" class="btn btn-secondary w-100">Volver al Login</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
