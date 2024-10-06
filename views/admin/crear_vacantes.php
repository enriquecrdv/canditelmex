<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

include '../../db/db.php';

$successMessage = '';
$errorMessage = '';

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
        $successMessage = "Vacante creada exitosamente.";
    } else {
        $errorMessage = "Error al crear la vacante: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nueva Vacante</title>
    <link rel="stylesheet" href="../../css/crear_vacantes.css">
</head>
<body>

<div id="crear-vacante-container" class="bootstrap-encapsulated">
    <h1>Crear Nueva Vacante</h1>

    <!-- Mensaje de éxito o error -->
    <?php if ($successMessage) { ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php } elseif ($errorMessage) { ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
    <?php } ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" class="form-control" name="nombre" placeholder="Escribe el nombre de la vacante" required>
        </div>

        <div class="mb-3">
            <label for="departamento" class="form-label">Departamento:</label>
            <input type="text" class="form-control" name="departamento" placeholder="Departamento al que pertenece" required>
        </div>

        <div class="mb-3">
            <label for="numero_id" class="form-label">Número ID:</label>
            <input type="text" class="form-control" name="numero_id" placeholder="ID único de la vacante" required>
        </div>

        <div class="mb-3">
            <label for="perfil" class="form-label">Perfil:</label>
            <textarea name="perfil" class="form-control" rows="4" placeholder="Perfil deseado para el puesto" required></textarea>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción:</label>
            <textarea name="descripcion" class="form-control" rows="4" placeholder="Descripción del puesto" required></textarea>
        </div>

        <div class="mb-3">
            <label for="requisitos" class="form-label">Requisitos:</label>
            <textarea name="requisitos" class="form-control" rows="4" placeholder="Requisitos necesarios para aplicar" required></textarea>
        </div>

        <div class="mb-3">
            <label for="foto" class="form-label">URL de la Foto:</label>
            <input type="text" class="form-control" name="foto" placeholder="URL de la imagen de la vacante (opcional)">
        </div>

        <div class="mb-3">
            <label for="prioridad" class="form-label">Prioridad:</label>
            <select name="prioridad" class="form-select" required>
                <option value="0">Baja</option>
                <option value="1">Media</option>
                <option value="2">Alta</option>
            </select>
        </div>

        <input type="submit" value="Crear Vacante" class="btn btn-primary w-100">
    </form>

    <a href="admin.php" class="btn btn-link">Volver al Panel de Admin</a>
</div>

<!-- Bootstrap solo encapsulado en el formulario -->
<style>
    .bootstrap-encapsulated .form-control,
    .bootstrap-encapsulated .form-select,
    .bootstrap-encapsulated .btn,
    .bootstrap-encapsulated .alert {
        /* Estilos de Bootstrap solo aplicados dentro de este contenedor */
        font-family: inherit; /* Evita que cambie la tipografía del layout */
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</body>
</html>

<?php
$content = ob_get_clean();
include 'admin_layout.php';
?>
