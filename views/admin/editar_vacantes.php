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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Vacante</title>
    <link rel="stylesheet" href="../../css/editar_vacantes.css">
</head>
<body>

<div id="editar-vacante-container" class="bootstrap-encapsulated">
    <?php if ($vacante_seleccionada) { ?>
    <!-- Formulario para editar los datos de la vacante -->
    <form method="POST" action="../../back/guardar_cambios_vacante.php">
        <input type="hidden" name="id" value="<?php echo $vacante_seleccionada['id']; ?>">

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" class="form-control" name="nombre" value="<?php echo $vacante_seleccionada['nombre']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="departamento" class="form-label">Departamento:</label>
            <input type="text" class="form-control" name="departamento" value="<?php echo $vacante_seleccionada['departamento']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="numero_id" class="form-label">Número ID:</label>
            <input type="text" class="form-control" name="numero_id" value="<?php echo $vacante_seleccionada['numero_id']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha:</label>
            <input type="date" class="form-control" name="fecha" value="<?php echo $vacante_seleccionada['fecha']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="perfil" class="form-label">Perfil:</label>
            <textarea name="perfil" class="form-control" rows="4" required><?php echo $vacante_seleccionada['perfil']; ?></textarea>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción:</label>
            <textarea name="descripcion" class="form-control" rows="4" required><?php echo $vacante_seleccionada['descripcion']; ?></textarea>
        </div>

        <div class="mb-3">
            <label for="requisitos" class="form-label">Requisitos:</label>
            <textarea name="requisitos" class="form-control" rows="4" required><?php echo $vacante_seleccionada['requisitos']; ?></textarea>
        </div>

        <div class="mb-3">
            <label for="foto" class="form-label">URL de la Foto:</label>
            <input type="text" class="form-control" name="foto" value="<?php echo $vacante_seleccionada['foto']; ?>" required>
        </div>

        <input type="submit" value="Actualizar Vacante" class="btn btn-primary w-100">
    </form>
    <?php } else { ?>
    <p>No se encontró la vacante seleccionada.</p>
    <?php } ?>

    <a href="lista_vacantes.php" class="btn btn-link">Volver a la Lista de Vacantes</a>
</div>

<!-- Bootstrap encapsulado solo para este contenedor -->
<style>
    .bootstrap-encapsulated .form-control,
    .bootstrap-encapsulated .btn,
    .bootstrap-encapsulated .btn-primary,
    .bootstrap-encapsulated .btn-link {
        /* Evitar interferencias de estilos globales */
        font-family: inherit;
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</body>
</html>

<?php
$content = ob_get_clean();
include 'admin_layout.php';
?>
