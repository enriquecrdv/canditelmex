<?php
session_start();
include 'db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $user_captcha = $_POST['captcha'];
    $generated_captcha = $_POST['captcha_value'];

    // Validación de la contraseña
    if (!preg_match('/^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{3,10}$/', $password)) {
        $message = "La contraseña debe tener entre 3 y 10 caracteres, al menos una mayúscula y un número.";
    } elseif ($user_captcha === $generated_captcha) {
        $check_sql = "SELECT * FROM users WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param('s', $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $message = "El nombre de usuario ya existe. Elige otro.";
        } else {
            $sql = "INSERT INTO users (username, password, role, nombre, email) 
                    VALUES (?, ?, 'user', ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssss', $username, $password, $nombre, $email);
            
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
    <style>
 /* Encapsulamos los estilos */
.bootstrap-encapsulated {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Fondo de la página */
body, html {
    background-color: #1c3d5a; /* Azul marino oscuro */
    color: #f5f5f5;
    height: 100%;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Contenedor principal */
.bootstrap-encapsulated .card {
    width: 100%;
    max-width: 500px; /* Ancho máximo del formulario */
    background-color: #2a5477; /* Azul más claro */
    border-radius: 15px;
    padding: 60px;
    box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
    color: #ffffff;
    margin: 0 auto;
}

.bootstrap-encapsulated .card-header {
    background-color: #1f4b6e;
    padding: 15px;
    border-radius: 10px 10px 0 0;
    text-align: center;
    font-size: 1.5rem;
}

/* Alinear todos los campos en una columna */
.bootstrap-encapsulated .form-group {
    display: flex;
    flex-direction: column;
    margin-bottom: 15px; /* Añade espacio entre los campos */
}

.bootstrap-encapsulated .form-control {
    width: 100%; /* Asegura que ocupen todo el ancho */
    margin: 0 auto; /* Centrar si es necesario */
    border-radius: 10px;
    background-color: rgba(255, 255, 255, 0.2);
    border: none;
    padding: 10px;
    color: #ffffff;
}

.bootstrap-encapsulated .form-control:focus {
    background-color: rgba(255, 255, 255, 0.3);
}

/* Estilo para los botones */
.bootstrap-encapsulated .btn-primary {
    background-color: #ffb100;
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: bold;
    padding: 10px;
    transition: background-color 0.3s ease, transform 0.2s ease;
    width: 100%;
    color: #1c3d5a;
    margin-top: 10px;
}

.bootstrap-encapsulated .btn-primary:hover {
    background-color: #ff9800;
}

.bootstrap-encapsulated .btn-secondary {
    background-color: #e0e0e0;
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    padding: 10px;
    transition: background-color 0.3s ease;
    width: 100%;
    color: #1c3d5a;
    margin-top: 10px;
}

/* Estilo de los placeholders */
.bootstrap-encapsulated .form-control::placeholder {
    color: rgba(255, 255, 255, 0.6);
    font-style: italic;
}

/* Validación de campos */
.bootstrap-encapsulated .form-control:invalid {
    border: 2px solid #e74c3c;
}

.bootstrap-encapsulated .form-control:valid {
    border: 2px solid #2ecc71;
}

/* Estilo para el CAPTCHA */
.bootstrap-encapsulated canvas {
    border: 2px solid #1f4b6e;
    border-radius: 10px;
    display: block;
    margin: 10px auto;
    background-color: #f5f5f5;
}

/* Animación para el formulario */
.bootstrap-encapsulated .card {
    animation: fadeIn 0.6s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Mejorar la respuesta para pantallas pequeñas */
@media (max-width: 768px) {
    .bootstrap-encapsulated .card {
        width: 90%;
    }
}

    </style>
</head>
<body>
    
<div class="bootstrap-encapsulated">
    <div class="center-container">
        <div class="card">
            <div class="card-header">
                Registro de Usuario
            </div>
            <div class="card-body">
                <?php if (isset($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>
                
                <form method="POST" action="" id="register-form">
                    <div class="form-group">
                        <label for="username" class="form-label">Nombre de Usuario</label>
                        <input type="text" name="username" placeholder="Ejemplo: usuario123" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" name="password" placeholder="Mínimo 3 caracteres, 1 mayúscula, 1 número" id="password" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="nombre" class="form-label">Nombre Completo</label>
                        <input type="text" name="nombre" placeholder="Tu nombre completo" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" placeholder="correo@ejemplo.com" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <canvas id="captchaCanvas" width="200" height="60"></canvas>
                        <input type="text" name="captcha" placeholder="Ingresa las letras de la imagen" id="captcha_input" class="form-control mt-2" required>
                        <input type="hidden" name="captcha_value" id="captcha_value">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </form>

                <br>
                <a href="login.php" class="btn btn-secondary">Volver al Login</a>
            </div>
        </div>
    </div>
</div>

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
        const captchaText = generateRandomText(6);
        document.getElementById('captcha_value').value = captchaText;

        context.clearRect(0, 0, canvas.width, canvas.height);
        context.font = '30px Arial';
        context.fillStyle = '#333';
        context.fillText(captchaText, 40, 40);

        for (let i = 0; i < 5; i++) {
            context.beginPath();
            context.moveTo(Math.random() * canvas.width, Math.random() * canvas.height);
            context.lineTo(Math.random() * canvas.width, Math.random() * canvas.height);
            context.stroke();
        }
    }

    document.getElementById('register-form').addEventListener('submit', function(event) {
        const password = document.getElementById('password').value;
        const userCaptcha = document.getElementById('captcha_input').value;
        const correctCaptcha = document.getElementById('captcha_value').value;

        if (!/^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{3,10}$/.test(password)) {
            event.preventDefault();
            alert("La contraseña debe tener entre 3 y 10 caracteres, al menos una mayúscula y un número.");
            return;
        }

        if (userCaptcha !== correctCaptcha) {
            event.preventDefault();
            alert("Captcha incorrecto. Intenta de nuevo.");
            drawCaptcha();
        }
    });

    window.onload = function() {
        drawCaptcha();
    }
</script>
</body>
</html>
