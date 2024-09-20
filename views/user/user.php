<?php
session_start();
if ($_SESSION['role'] != 'user') {
    header("Location: ../../login.php");
    exit;
}
?>
<div class="container">
    <h1>Bienvenido, Usuario</h1>
    <p>Busca vacantes aqui.</p>
    <a href="../../logout.php">Cerrar sesión</a>
    </div>
<?php
$content = ob_get_clean();
include 'user_layout.php'; // Utiliza un layout específico para los usuarios, si existe
?>
