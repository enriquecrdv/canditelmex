<!-- admin_layout.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <style>
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #f8f9fa;
            padding: 1rem;
        }

        .main-content {
            margin-left: 250px;
            padding: 1rem;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h4>Admin Panel</h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="lista_vacantes.php">Lista de Vacantes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="crear_vacantes.php">Crear Vacante</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../../logout.php">Cerrar Sesión</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../restablecer_contra.php">Restablecer contraseña</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../eliminar_cuenta.php">Eliminar cuenta</a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="container">
        <?php echo $content; ?>
        </div>
    </div>
</body>

</html>