<?php
session_start();
if ($_SESSION['role'] != 'user') {
    header("Location: ../../login.php");
    exit;
}

include '../../db/db.php';

// Obtener el ID del usuario (asumido que está almacenado en la sesión)
$usuario_id = $_SESSION['id'];

// Manejar la subida de la prueba técnica
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aplicacion_id'])) {
    $aplicacion_id = intval($_POST['aplicacion_id']);
    $prueba_tecnica = $_FILES['respuesta_prueba_tecnica']['tmp_name'];
    $prueba_tecnica_error = $_FILES['respuesta_prueba_tecnica']['error'];

    // Verificar si se subió la prueba técnica
    if ($prueba_tecnica_error === UPLOAD_ERR_OK) {
        $prueba_tecnica_Content = file_get_contents($prueba_tecnica);
        
        $sql_update_prueba = "UPDATE aplicaciones SET prueba_tecnica = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update_prueba);
        $stmt->bind_param('si', $prueba_tecnica_Content, $aplicacion_id);

        if ($stmt->execute()) {
            $message = "Prueba técnica actualizada correctamente.";
        } else {
            $message = "Error al actualizar: " . $stmt->error;
        }
    } else {
        $message = "Error al cargar el archivo: " . $prueba_tecnica_error;
    }
}

// Obtener las aplicaciones del usuario con detalles de la vacante, ordenadas por estado
$sql = "SELECT a.*, v.nombre AS vacante_nombre, v.departamento, v.perfil, v.descripcion, v.requisitos, v.foto, v.fecha
        FROM aplicaciones a 
        JOIN vacantes v ON a.vacante_id = v.id
        WHERE a.usuario_id = ? AND v.estado = 'abierta' 
        ORDER BY 
        CASE 
            WHEN a.estado = 'aprobada' THEN 1
            WHEN a.estado = 'pendiente' THEN 2
            WHEN a.estado = 'rechazada' THEN 3
        END ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

// Buffer de salida
ob_start();
?>

<div class="container">
    <h1>Mis Aplicaciones</h1>
    
    <?php if (isset($message)) { ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php } ?>

    <?php if ($result->num_rows > 0) { ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vacante</th>
                    <th>Departamento</th>
                    <th>Perfil</th>
                    <th>Descripción</th>
                    <th>Requisitos</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Prueba Técnica</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($aplicacion = $result->fetch_assoc()) { ?>
                    <tr class="<?php echo ($aplicacion['estado'] == 'aprobada') ? 'table-success' : ($aplicacion['estado'] == 'rechazada' ? 'table-danger' : ''); ?>">
                        <td><?php echo htmlspecialchars($aplicacion['id']); ?></td>
                        <td><?php echo htmlspecialchars($aplicacion['vacante_nombre']); ?></td>
                        <td><?php echo htmlspecialchars($aplicacion['departamento']); ?></td>
                        <td><?php echo htmlspecialchars($aplicacion['perfil']); ?></td>
                        <td><?php echo htmlspecialchars($aplicacion['descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($aplicacion['requisitos']); ?></td>
                        <td><?php echo htmlspecialchars($aplicacion['fecha']); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($aplicacion['estado'])); ?></td>
                        <td>
                            <?php if ($aplicacion['prueba_tecnica']) { ?>
                                <a href="download_prueba.php?aplicacion_id=<?php echo htmlspecialchars($aplicacion['id']); ?>" target="_blank">Descargar prueba técnica</a>
                                <form method="POST" enctype="multipart/form-data" class="mt-2">
                                    <input type="hidden" name="aplicacion_id" value="<?php echo htmlspecialchars($aplicacion['id']); ?>">
                                    <div class="mb-2">
                                        Subir respuesta de prueba técnica
                                        <input type="file" name="respuesta_prueba_tecnica" class="form-control">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Subir respuesta</button>
                                </form>
                            <?php } else { ?>
                                No disponible
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <div class="alert alert-info">No has aplicado a ninguna vacante aún.</div>
    <?php } ?>
    
    <br>
    <a href="../../index.php" class="btn btn-secondary">Volver al Inicio</a>
</div>

<?php
$content = ob_get_clean();
include 'user_layout.php';
?>
