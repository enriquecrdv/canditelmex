<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

include '../../db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $departamento = $_POST['departamento'];
    $numero_id = $_POST['numero_id'];
    $perfil = $_POST['perfil'];
    $descripcion = $_POST['descripcion'];
    $requisitos = $_POST['requisitos'];
    $foto = $_POST['foto'];
    $prioridad = $_POST['prioridad'];

    $sql = "INSERT INTO vacantes (nombre, departamento, numero_id, fecha, perfil, descripcion, requisitos, foto, prioridad) 
         VALUES ('$nombre', '$departamento', '$numero_id', CURDATE(), '$perfil', '$descripcion', '$requisitos', '$foto', '$prioridad')";
         
    if ($conn->query($sql) === TRUE) {
        echo "Vacante creada exitosamente.";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<div class="container" style="padding-left: 30px;">
    <h1>Crear Nueva Vacante</h1>
    <form method="POST" action="">
        <label for="nombre">Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label for="departamento">Departamento:</label><br>
        <input type="text" name="departamento" required><br><br>

        <label for="numero_id">Número ID:</label><br>
        <input type="text" name="numero_id" required><br><br>

        <label for="perfil">Perfil:</label><br>
        <textarea name="perfil" rows="5" required></textarea><br><br>

        <label for="descripcion">Descripción:</label><br>
        <textarea name="descripcion" rows="5" required></textarea><br><br>

        <label for="requisitos">Requisitos:</label><br>
        <textarea name="requisitos" rows="5" required></textarea><br><br>

        <label for="foto">URL de la Foto:</label><br>
        <input type="text" name="foto"><br><br>

        <label for="prioridad">Prioridad:</label><br>
        <select name="prioridad" required>
            <option value="0">Baja</option>
            <option value="1">Media</option>
            <option value="2">Alta</option>
        </select><br><br>

        <input type="submit" value="Crear Vacante">
    </form>

    <br>
    <a href="admin.php">Volver al Panel de Admin</a>
</div>

<?php
$content = ob_get_clean();
include 'admin_layout.php';
?>