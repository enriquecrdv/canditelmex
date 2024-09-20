<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}
?>
<div class="container">
    <h1>Bienvenido, Administrador</h1>
    <p>Publica tus vacantes aqui.</p>

    <h3>Opciones de Vacantes:</h3>
    <ul>
        <li><a href="lista_vacantes.php">Lista de Vacantes</a></li>
        <li><a href="crear_vacantes.php">Crear Nueva Vacante</a></li>
    </ul>

    <a href="../logout.php">Cerrar sesión</a>
</div>

<?php
$content = ob_get_clean();
include 'admin_layout.php';
?>