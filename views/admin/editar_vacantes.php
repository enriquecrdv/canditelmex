<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

include '../../db/db.php';

$vacante_seleccionada = null;

// Si el ID de la vacante está presente en la URL o en el formulario
if (isset($_GET['vacante_id'])) {
    $id = $_GET['vacante_id'];
} elseif (isset($_POST['vacante_id'])) {
    $id = $_POST['vacante_id'];
}

if (isset($id)) {
    // Obtener los datos de la vacante seleccionada
    $sql = "SELECT * FROM vacantes WHERE id='$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $vacante_seleccionada = $result->fetch_assoc();
    }
}
?>

<div class="container">

    <?php if ($vacante_seleccionada) { ?>
    <!-- Formulario para editar los datos de la vacante -->
    <form method="POST" action="../../back/guardar_cambios_vacante.php">
        <input type="hidden" name="id" value="<?php echo $vacante_seleccionada['id']; ?>">

        <label for="nombre">Nombre:</label><br>
        <input type="text" name="nombre" value="<?php echo $vacante_seleccionada['nombre']; ?>" required><br><br>

        <label for="departamento">Departamento:</label><br>
        <input type="text" name="departamento" value="<?php echo $vacante_seleccionada['departamento']; ?>"
            required><br><br>

        <label for="numero_id">Número ID:</label><br>
        <input type="text" name="numero_id" value="<?php echo $vacante_seleccionada['numero_id']; ?>" required><br><br>

        <label for="fecha">Fecha:</label><br>
        <input type="date" name="fecha" value="<?php echo $vacante_seleccionada['fecha']; ?>" required><br><br>

        <label for="perfil">Perfil:</label><br>
        <textarea name="perfil" rows="5" required><?php echo $vacante_seleccionada['perfil']; ?></textarea><br><br>

        <label for="descripcion">Descripción:</label><br>
        <textarea name="descripcion" rows="5"
            required><?php echo $vacante_seleccionada['descripcion']; ?></textarea><br><br>

        <label for="requisitos">Requisitos:</label><br>
        <textarea name="requisitos" rows="5"
            required><?php echo $vacante_seleccionada['requisitos']; ?></textarea><br><br>

        <label for="foto">URL de la Foto:</label><br>
        <input type="text" name="foto" value="<?php echo $vacante_seleccionada['foto']; ?>" required><br><br>

        <input type="submit" value="Actualizar Vacante">
    </form>
    <?php } else { ?>
    <p>No se encontró la vacante seleccionada.</p>
    <?php } ?>

    <br>
    <a href="lista_vacantes.php">Volver a la Lista de Vacantes</a>
</div>
<?php
$content = ob_get_clean();
include 'admin_layout.php';
?>