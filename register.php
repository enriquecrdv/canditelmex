<?php
session_start();
include 'db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; // Contraseña sin encriptar
    $nombre = $_POST['nombre'];
    $email = $_POST['email']; // Nuevo campo para el correo electrónico
    $user_captcha = $_POST['captcha']; // Captcha ingresado por el usuario
    $generated_captcha = $_POST['captcha_value']; // Captcha generado en el frontend

    // Validar CAPTCHA en el servidor
    if ($user_captcha === $generated_captcha) {
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
    } else {
        $message = "Captcha incorrecto.";
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

        canvas {
            border: 1px solid #ccc;
            display: block;
            margin: 0 auto;
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
                
                <form method="POST" action="" id="register-form">
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
                    
                    <!-- CAPTCHA generado dinámicamente con una imagen -->
                    <div class="mb-3">
                        <canvas id="captchaCanvas" width="200" height="60"></canvas>
                        <input type="text" name="captcha" id="captcha_input" class="form-control mt-2" placeholder="Ingresa las letras de la imagen" required>
                        <input type="hidden" name="captcha_value" id="captcha_value"> <!-- Valor generado -->
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Registrar</button>
                </form>

                <br>
                <a href="login.php" class="btn btn-secondary w-100">Volver al Login</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Generar letras aleatorias
        function generateRandomText(length) {
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let result = '';
            for (let i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            return result;
        }

        // Dibujar el CAPTCHA en el canvas
        function drawCaptcha() {
            const canvas = document.getElementById('captchaCanvas');
            const context = canvas.getContext('2d');
            const captchaText = generateRandomText(6);  // Generar texto de 6 caracteres
            document.getElementById('captcha_value').value = captchaText;  // Asignar el valor generado al input hidden
            console.log(document.getElementById('captcha_value').value);

            // Limpiar el canvas antes de dibujar
            context.clearRect(0, 0, canvas.width, canvas.height);

            // Estilos del CAPTCHA
            context.font = '30px Arial';
            context.fillStyle = '#333';
            context.fillText(captchaText, 40, 40);  // Dibuja el texto en el canvas

            // Añadir ruido o distorsión (opcional)
            for (let i = 0; i < 5; i++) {
                context.beginPath();
                context.moveTo(Math.random() * canvas.width, Math.random() * canvas.height);
                context.lineTo(Math.random() * canvas.width, Math.random() * canvas.height);
                context.stroke();
            }
        }

        // Ejecutar cuando la página esté lista
        window.onload = function() {
            drawCaptcha();
        }

        // Validación del CAPTCHA en el frontend antes de enviar el formulario
        document.getElementById('register-form').addEventListener('submit', function(event) {
            let userCaptcha = document.getElementById('captcha_input').value;
            let correctCaptcha = document.getElementById('captcha_value').value;

            if (userCaptcha !== correctCaptcha) {
                event.preventDefault();
                alert("Captcha incorrecto. Intenta de nuevo.");
                drawCaptcha();  // Regenerar CAPTCHA si falla
            }
        });
    </script>
</body>
</html>
