<?php
session_start();
if ($_SESSION['role'] != 'user') {
    header("Location: ../../login.php");
    exit;
}

include '../../db/db.php';

$usuario_id = $_SESSION['id'];
$vacante_id = isset($_GET['vacante_id']) ? intval($_GET['vacante_id']) : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $carrera = $_POST['carrera'];
    $anos_experiencia = isset($_POST['anos_experiencia']) ? intval($_POST['anos_experiencia']) : 0;
    $cv = $_FILES['cv']['tmp_name'];
    $cv_error = $_FILES['cv']['error'];

    if ($cv_error === UPLOAD_ERR_OK) {
        $cvContent = file_get_contents($cv);

        // Guardar los datos de la aplicación en la base de datos
        $sql = "INSERT INTO aplicaciones (vacante_id, usuario_id, nombre, email, telefono, carrera, anos_experiencia, cv) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iissssis', $vacante_id, $usuario_id, $nombre, $email, $telefono, $carrera, $anos_experiencia, $cvContent);

        if ($stmt->execute()) {
            $message = "Aplicación enviada exitosamente.";
        } else {
            $message = "Error al enviar la aplicación: " . $stmt->error;
        }
    } else {
        $message = "Error al cargar el archivo: " . $cv_error;
    }
}
?>

<div class="container">
    <h1>Aplicar a Vacante</h1>

    <?php if (isset($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono:</label>
            <input type="text" name="telefono" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="carrera" class="form-label">Carrera:</label>
            <input type="text" name="carrera" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="anos_experiencia" class="form-label">Años de Experiencia:</label>
            <input type="number" name="anos_experiencia" class="form-control" required min="0">
        </div>
        <div class="mb-3">
            <label for="cv" class="form-label">CV (PDF):</label>
            <input type="file" name="cv" class="form-control" accept=".pdf" required>
        </div>
        <button type="submit" class="btn btn-primary">Enviar Aplicación</button>
    </form>

    <br>
    <a href="usuario_vacantes.php" class="btn btn-secondary">Volver a Buscar Vacantes</a>
</div>
<?php
$content = ob_get_clean();
include 'user_layout.php';
?>
