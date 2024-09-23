<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

include '../../db/db.php';

// Obtener el ID de la vacante para filtrar las aplicaciones
$vacante_id = isset($_GET['vacante_id']) ? intval($_GET['vacante_id']) : 0;

// Obtener el valor de búsqueda para nombre, carrera y años de experiencia
$search_term = isset($_POST['search_term']) ? trim($_POST['search_term']) : '';
$anos_experiencia = isset($_POST['anos_experiencia']) ? intval($_POST['anos_experiencia']) : -1;

// Guardar la entrevista programada, prueba técnica, calificación, observaciones y actualizar el estado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aplicacion_id'])) {
    $aplicacion_id = intval($_POST['aplicacion_id']);
    $message = '';

    // Verificar si se programó la entrevista
    if (isset($_POST['fecha_entrevista']) && !empty($_POST['fecha_entrevista'])) {
        $fecha_entrevista = $_POST['fecha_entrevista'];
        $sql_update_entrevista = "UPDATE aplicaciones SET entrevista_datetime = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update_entrevista);
        $stmt->bind_param('si', $fecha_entrevista, $aplicacion_id);
        if ($stmt->execute()) {
            $message = "Entrevista programada exitosamente.";
        } else {
            $message = "Error al programar la entrevista: " . $conn->error;
        }
    }

    // Verificar si se cambió el estado de la aplicación
    if (isset($_POST['estado'])) {
        $nuevo_estado = $_POST['estado'];
        $sql_update_estado = "UPDATE aplicaciones SET estado = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update_estado);
        $stmt->bind_param('si', $nuevo_estado, $aplicacion_id);
        if ($stmt->execute()) {
            $message = "Estado de la aplicación actualizado exitosamente.";
        } else {
            $message = "Error al actualizar el estado: " . $conn->error;
        }
    }

    // Subir prueba técnica al servidor
    $prueba_tecnica = $_FILES['prueba_tecnica']['tmp_name'];
    $prueba_tecnica_error = $_FILES['prueba_tecnica']['error'];
    // Subir prueba técnica al servidor
    if ($prueba_tecnica_error === UPLOAD_ERR_OK) {
        $prueba_tecnica_Content = file_get_contents($prueba_tecnica);
        
        $sql_update_prueba = "UPDATE aplicaciones SET prueba_tecnica = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update_prueba);
        $stmt->bind_param('si', $prueba_tecnica_Content, $aplicacion_id);

        if ($stmt->execute()) {
            $message = "Actualizado correctamente.";
        } else {
            $message = "Error al actualizar: " . $stmt->error;
        }
    } else {
        $message = "Error al cargar el archivo: " . $prueba_tecnica_error;
    }

    // Guardar calificación de la prueba técnica
    if (isset($_POST['calificacion_prueba'])) {
        $calificacion = intval($_POST['calificacion_prueba']);
        $sql_update_calificacion = "UPDATE aplicaciones SET calificacion_prueba = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update_calificacion);
        $stmt->bind_param('ii', $calificacion, $aplicacion_id);
        if ($stmt->execute()) {
            $message = "Calificación guardada exitosamente.";
        } else {
            $message = "Error al actualizar: " . $conn->error;
        }
    }

    // Guardar observaciones
    if (isset($_POST['observaciones'])) {
        $observaciones = $_POST['observaciones'];
        $sql_update_observaciones = "UPDATE aplicaciones SET observaciones = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update_observaciones);
        $stmt->bind_param('si', $observaciones, $aplicacion_id);
        if ($stmt->execute()) {
            $message = "Actualizado correctamente.";
        } else {
            $message = "Error al guardar las observaciones: " . $conn->error;
        }
    }
}

// Filtrar aplicaciones por nombre, carrera y años de experiencia usando el mismo input
$filter_condition = '';
if (!empty($search_term)) {
    $search_term_escaped = $conn->real_escape_string($search_term);
    $filter_condition .= " AND (a.nombre LIKE '%$search_term_escaped%' OR a.carrera LIKE '%$search_term_escaped%')";
}

// Filtrar por años de experiencia
if ($anos_experiencia >= 0) {
    if ($anos_experiencia > 5) {
        $filter_condition .= " AND a.anos_experiencia > 5";
    } else {
        $filter_condition .= " AND a.anos_experiencia = $anos_experiencia";
    }
}

// Obtener las aplicaciones asociadas a la vacante, ordenadas por estado
$sql = "SELECT * FROM aplicaciones a WHERE a.vacante_id='$vacante_id' $filter_condition ORDER BY 
        CASE 
            WHEN a.estado = 'aprobada' THEN 1
            WHEN a.estado = 'pendiente' THEN 2
            WHEN a.estado = 'rechazada' THEN 3
        END ASC";
$result = $conn->query($sql);

// Buffer de salida
ob_start();
?>

<div class="container">
    <h1>Aplicaciones para la Vacante ID: <?php echo $vacante_id; ?></h1>

    <?php if (isset($message)) {
        echo "<div class='alert alert-info'>$message</div>";
    } ?>

    <!-- Formulario de búsqueda -->
    <form method="POST" class="mb-3">
        <div class="mb-2">
            Buscar por nombre o carrera
            <input type="text" name="search_term" class="form-control"
                value="<?php echo htmlspecialchars($search_term); ?>">
        </div>
        <div class="mb-2">
            Filtrar por años de experiencia
            <select name="anos_experiencia" class="form-select">
                <option value="-1" <?php echo ($anos_experiencia == -1) ? 'selected' : ''; ?>>Todos</option>
                <option value="0" <?php echo ($anos_experiencia == 0) ? 'selected' : ''; ?>>0 años</option>
                <option value="1" <?php echo ($anos_experiencia == 1) ? 'selected' : ''; ?>>1 año</option>
                <option value="2" <?php echo ($anos_experiencia == 2) ? 'selected' : ''; ?>>2 años</option>
                <option value="3" <?php echo ($anos_experiencia == 3) ? 'selected' : ''; ?>>3 años</option>
                <option value="4" <?php echo ($anos_experiencia == 4) ? 'selected' : ''; ?>>4 años</option>
                <option value="5" <?php echo ($anos_experiencia == 5) ? 'selected' : ''; ?>>5 años</option>
                <option value="6" <?php echo ($anos_experiencia > 5) ? 'selected' : ''; ?>>Más de 5 años</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>

    <!-- Botón para exportar a CSV -->
    <form method="POST" action="export_to_csv.php" class="mb-3">
        <input type="hidden" name="vacante_id" value="<?php echo $vacante_id; ?>">
        <input type="hidden" name="search_term" value="<?php echo htmlspecialchars($search_term); ?>">
        <input type="hidden" name="anos_experiencia" value="<?php echo htmlspecialchars($anos_experiencia); ?>">
        <button type="submit" class="btn btn-success">Exportar a CSV</button>
    </form>


    <?php if ($result->num_rows > 0) { ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>CV</th>
                <th>Estado</th>
                <th>Fecha Entrevista</th>
                <th>Prueba Técnica</th>
                <th>Calificación</th>
                <th>Carrera</th>
                <th>Años de Experiencia</th>
                <th>Observaciones</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($aplicacion = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($aplicacion['nombre']); ?></td>
                <td><?php echo htmlspecialchars($aplicacion['email']); ?></td>
                <td><?php echo htmlspecialchars($aplicacion['telefono']); ?></td>
                <td>
                    <?php if ($aplicacion['cv']) { ?>
                    <a href="../../back/download_cv.php?id=<?php echo $aplicacion['id']; ?>">Descargar CV</a>
                    <?php } else { ?>
                    No disponible
                    <?php } ?>
                </td>
                </td>
                <td><?php echo htmlspecialchars($aplicacion['estado']); ?></td>
                <td><?php echo $aplicacion['entrevista_datetime'] ? htmlspecialchars($aplicacion['entrevista_datetime']) : 'No programada'; ?>
                </td>
                <td>
                    <?php if ($aplicacion['prueba_tecnica']) { ?>
                    <a href="../../back/download_prueba.php?id=<?php echo $aplicacion['id']; ?>">Descargar Prueba tecnica</a>
                    <?php } else { ?>
                    No disponible
                    <?php } ?>
                </td>
                <td><?php echo $aplicacion['calificacion_prueba'] !== null ? htmlspecialchars($aplicacion['calificacion_prueba']) : 'No calificada'; ?>
                </td>
                <td><?php echo htmlspecialchars($aplicacion['carrera']); ?></td>
                <td><?php echo htmlspecialchars($aplicacion['anos_experiencia']); ?></td>
                <td><?php echo htmlspecialchars($aplicacion['observaciones']); ?></td>
                <td>
                    <!-- Formulario para actualizar el estado, prueba técnica, calificación y observaciones -->
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="aplicacion_id" value="<?php echo $aplicacion['id']; ?>">
                        <div class="mb-2">
                            <label for="estado_<?php echo $aplicacion['id']; ?>" class="form-label">Cambiar
                                estado:</label>
                            <select name="estado" id="estado_<?php echo $aplicacion['id']; ?>" class="form-select">
                                <option value="pendiente"
                                    <?php echo ($aplicacion['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente
                                </option>
                                <option value="aprobada"
                                    <?php echo ($aplicacion['estado'] == 'aprobada') ? 'selected' : ''; ?>>Aprobada
                                </option>
                                <option value="rechazada"
                                    <?php echo ($aplicacion['estado'] == 'rechazada') ? 'selected' : ''; ?>>Rechazada
                                </option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="fecha_entrevista_<?php echo $aplicacion['id']; ?>" class="form-label">Fecha de
                                Entrevista:</label>
                            <input type="datetime-local" name="fecha_entrevista"
                                id="fecha_entrevista_<?php echo $aplicacion['id']; ?>" class="form-control"
                                value="<?php echo $aplicacion['entrevista_datetime'] ? date('Y-m-d\TH:i', strtotime($aplicacion['entrevista_datetime'])) : ''; ?>">
                        </div>
                        <div class="mb-2">
                            <label for="prueba_tecnica_<?php echo $aplicacion['id']; ?>" class="form-label">Subir prueba
                                técnica:</label>
                            <input type="file" name="prueba_tecnica"
                                id="prueba_tecnica_<?php echo $aplicacion['id']; ?>" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label for="calificacion_prueba_<?php echo $aplicacion['id']; ?>"
                                class="form-label">Calificación prueba técnica:</label>
                            <input type="number" name="calificacion_prueba"
                                id="calificacion_prueba_<?php echo $aplicacion['id']; ?>" class="form-control" min="0"
                                max="10"
                                value="<?php echo $aplicacion['calificacion_prueba'] !== null ? htmlspecialchars($aplicacion['calificacion_prueba']) : ''; ?>">
                        </div>
                        <div class="mb-2">
                            <label for="observaciones_<?php echo $aplicacion['id']; ?>"
                                class="form-label">Observaciones:</label>
                            <textarea name="observaciones" id="observaciones_<?php echo $aplicacion['id']; ?>"
                                class="form-control"><?php echo htmlspecialchars($aplicacion['observaciones']); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php } else { ?>
    <p>No hay aplicaciones para esta vacante.</p>
    <?php } ?>
</div>

<?php
// Fin del buffer de salida
$content = ob_get_clean();
include 'admin_layout.php';

// Cerrar conexión
$conn->close();
?>