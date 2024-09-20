<?php
session_start();
if ($_SESSION['role'] != 'user') {
    header("Location: ../../login.php");
    exit;
}

include '../../db/db.php';

// Obtener el ID del usuario (asumido que está almacenado en la sesión)
$usuario_id = $_SESSION['id'];

// Obtener las aplicaciones del usuario con detalles de la vacante, ordenadas por estado
$sql = "SELECT a.*, v.nombre AS vacante_nombre, v.departamento, v.perfil, v.descripcion, v.requisitos, v.foto, v.fecha
        FROM aplicaciones a 
        JOIN vacantes v ON a.vacante_id = v.id
        WHERE a.usuario_id = ? AND v.estado = 'abierta'  -- Filtrar vacantes con estado abierto
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
include 'user_layout.php'; // Asegúrate de tener un layout para el usuario
?>
