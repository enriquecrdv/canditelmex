<?php
session_start();
include 'db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user_captcha = $_POST['captcha']; // Captcha ingresado por el usuario
    $generated_captcha = $_POST['captcha_value']; // Captcha generado en el frontend

    // Validar CAPTCHA en el servidor
    if ($user_captcha === $generated_captcha) {
        // Consulta preparada para evitar inyecciones SQL
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->bind_param('ss', $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['id'] = $user['id']; // Agregar el ID del usuario a la sesión

            // Redirección según el rol del usuario
            if ($user['role'] == 'admin') {
                header("Location: views/admin/admin.php");
            } else {
                header("Location: views/user/user.php");
            }
            exit(); // Salir después de redirigir
        } else {
            echo "Nombre de usuario o contraseña incorrectos";
        }
    } else {
        echo "Captcha incorrecto.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
 
    <link rel="stylesheet" href="./css/login.css?v=1.0">


 
</head>
<body>
    <div class="container mt-5">
        <div class="card text-center">
            <div class="card-header">
                <h5 class="card-title">Iniciar Sesión</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" id="login-form">
                    <div class="mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Nombre de usuario" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
                    </div>

                    <!-- CAPTCHA generado dinámicamente con una imagen -->
                    <div class="mb-3">
                        <canvas id="captchaCanvas" width="200" height="60"></canvas>
                        <input type="text" name="captcha" id="captcha_input" class="form-control mt-2" placeholder="Ingresa las letras de la imagen" required>
                        <input type="hidden" name="captcha_value" id="captcha_value"> <!-- Valor generado -->
                    </div>

                    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                </form>
                <div class="mt-3">
                    <a href="register.php" class="btn btn-secondary">Crear Cuenta</a>
                </div>
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
        document.getElementById('login-form').addEventListener('submit', function(event) {
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
