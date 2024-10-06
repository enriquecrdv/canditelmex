<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../../CSS/admin_layout.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <div class="sidebar">
        <h4><a href="admin.php" class="admin-panel-link">Admin Panel</a></h4>

        <!-- Opciones justo debajo de "Admin Panel" -->
        <ul class="nav flex-column">
            <li id="lista-vacantes-item" class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'lista_vacantes.php' ? 'active-link' : ''; ?>" href="lista_vacantes.php">
                    <span class="material-icons">view_list</span> Lista de Vacantes
                </a>
            </li>
            <li id="crear-vacante-item" class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'crear_vacantes.php' ? 'active-link' : ''; ?>" href="crear_vacantes.php">
                    <span class="material-icons">add_circle</span> Crear Vacante
                </a>
            </li>
        </ul>

        <!-- Íconos para las opciones de cuenta, abajo del todo -->
        <div class="nav-bottom">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="../../logout.php">
                        <span class="material-icons">exit_to_app</span> Cerrar Sesión
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../restablecer_contra.php">
                        <span class="material-icons">lock_reset</span> Restablecer Contraseña
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../eliminar_cuenta.php">
                        <span class="material-icons">delete</span> Eliminar Cuenta
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Bootstrap encapsulado -->
    <div class="main-content bootstrap-encapsulated">
        <div class="container">
            <?php echo $content; ?>
        </div>
    </div>

    <style>
        /* Encapsular estilos de Bootstrap para no interferir */
        .bootstrap-encapsulated .form-control,
        .bootstrap-encapsulated .btn,
        .bootstrap-encapsulated .btn-primary,
        .bootstrap-encapsulated .btn-link {
            font-family: inherit;
        }
    </style>

    <!-- Incluir Bootstrap solo dentro del contenedor encapsulado -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</body>

</html>

