<?php
session_start();
if ($_SESSION['role'] != 'user') {
    header("Location: ../../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>User Panel</title>
</head>
<body>
    <h1>Bienvenido, Usuario</h1>
    <p>Busca vacantes aqui.</p>
    <a href="../../logout.php">Cerrar sesión</a>
</body>
</html>
<?php
$content = ob_get_clean();
include 'user_layout.php'; // Utiliza un layout específico para los usuarios, si existe
?>
