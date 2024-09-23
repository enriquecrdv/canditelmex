<?php
session_start();
if ($_SESSION['role'] != 'user') {
    header("Location: ../../login.php");
    exit;
}
?>
<div class="container" style="padding-left: 30px;">
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
    <p>Busca vacantes aquí.</p>
    <a href="../../logout.php">Cerrar sesión</a>
</div>
<?php
$content = ob_get_clean();
include 'user_layout.php';
?>
