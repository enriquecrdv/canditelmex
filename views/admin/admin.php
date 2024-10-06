<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido, Administrador</title>
    <link rel="stylesheet" href="../../css/admin_welcome.css">
</head>
<body>

<!-- Encapsulamos los estilos de Bootstrap -->
<div id="welcome-container" class="bootstrap-encapsulated">
    <h1 id="welcome-title">Bienvenido, Administrador</h1>
    <p id="welcome-description">Publica tus vacantes aquí.</p>

    <h3 id="vacancy-options-title">Opciones de Vacantes:</h3>
    <ul id="vacancy-options-list">
        <li><a href="lista_vacantes.php" class="btn btn-link">Lista de Vacantes</a></li>
        <li><a href="crear_vacantes.php" class="btn btn-link">Crear Nueva Vacante</a></li>
    </ul>

    <a id="logout-btn" href="../logout.php" class="btn btn-primary">Cerrar sesión</a>
</div>

<!-- Encapsular los estilos de Bootstrap -->
<style>
    .bootstrap-encapsulated .btn,
    .bootstrap-encapsulated .btn-link,
    .bootstrap-encapsulated .btn-primary {
        /* Evitar que se modifique la tipografía y otros estilos del layout */
        font-family: inherit; 
        text-decoration: none;
    }
    
    .bootstrap-encapsulated .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        text-align: center;
        display: inline-block;
        transition: background-color 0.3s ease;
    }
    
    .bootstrap-encapsulated .btn-primary:hover {
        background-color: #0056b3;
    }

    .bootstrap-encapsulated .btn-link {
        color: #007bff;
        text-decoration: underline;
    }

    .bootstrap-encapsulated .btn-link:hover {
        color: #0056b3;
    }
</style>

<!-- Cargar Bootstrap solo para este contenedor -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</body>
</html>

<?php
$content = ob_get_clean();
include 'admin_layout.php';
?>
