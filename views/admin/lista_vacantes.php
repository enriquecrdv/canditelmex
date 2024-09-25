<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

include '../../db/db.php';

// Obtener todos los departamentos únicos
$sql_departamentos = "SELECT DISTINCT departamento FROM vacantes";
$result_departamentos = $conn->query($sql_departamentos);

// Obtener el departamento seleccionado para el filtro
$selected_departamento = isset($_GET['departamento']) ? $_GET['departamento'] : '';

// Eliminar la vacante si se envía el ID para eliminación
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql_delete = "DELETE FROM vacantes WHERE id='$delete_id'";

    if ($conn->query($sql_delete) === TRUE) {
        $message = "Vacante eliminada exitosamente.";
    } else {
        $message = "Error al eliminar la vacante: " . $conn->error;
    }

    // Redirigir después de eliminar para evitar múltiples eliminaciones si se actualiza la página
    header("Location: lista_vacantes.php");
    exit;
}

// Cambiar el tipo de vacante (liberar vacante)
if (isset($_GET['release_id'])) {
    $release_id = $_GET['release_id'];
    $current_type = $_GET['current_type'];
    $new_type = ($current_type == 'interno') ? 'externo' : 'interno';
    $sql_release = "UPDATE vacantes SET tipo='$new_type' WHERE id='$release_id'";

    if ($conn->query($sql_release) === TRUE) {
        $message = "Tipo de la vacante actualizado a $new_type.";
    } else {
        $message = "Error al cambiar el tipo de la vacante: " . $conn->error;
    }

    // Redirigir después de actualizar el tipo de vacante
    header("Location: lista_vacantes.php");
    exit;
}


// Cambiar el estado de la vacante si se envía el ID para actualización
if (isset($_GET['update_id'])) {
    $update_id = $_GET['update_id'];
    $current_status = $_GET['current_status'];
    $new_status = ($current_status == 'abierta') ? 'cerrada' : 'abierta';
    $sql_update = "UPDATE vacantes SET estado='$new_status' WHERE id='$update_id'";

    if ($conn->query($sql_update) === TRUE) {
        $message = "Estado de la vacante actualizado a $new_status.";
    } else {
        $message = "Error al actualizar el estado de la vacante: " . $conn->error;
    }

    // Redirigir después de actualizar el estado para evitar múltiples actualizaciones si se actualiza la página
    header("Location: lista_vacantes.php");
    exit;
}

// Destacar o quitar el destaque de una vacante
if (isset($_GET['highlight_id'])) {
    $highlight_id = $_GET['highlight_id'];
    $current_highlight = $_GET['current_highlight'];
    $new_highlight = ($current_highlight == '1') ? '0' : '1';
    $sql_highlight = "UPDATE vacantes SET destacada='$new_highlight' WHERE id='$highlight_id'";

    if ($conn->query($sql_highlight) === TRUE) {
        $message = "Vacante " . ($new_highlight == '1' ? "destacada" : "no destacada") . " exitosamente.";
    } else {
        $message = "Error al actualizar el destaque de la vacante: " . $conn->error;
    }

    // Redirigir después de actualizar el destaque para evitar múltiples actualizaciones si se actualiza la página
    header("Location: lista_vacantes.php");
    exit;
}

// Obtener vacantes filtradas
$sql = "
    SELECT 
        v.*, 
        COUNT(a.id) AS total_postulantes, 
        SUM(CASE WHEN a.estado = 'aprobada' THEN 1 ELSE 0 END) AS seleccionados
    FROM vacantes v
    LEFT JOIN aplicaciones a ON v.id = a.vacante_id
";

if ($selected_departamento != '') {
    $sql .= " WHERE v.departamento = '$selected_departamento'";
}

$sql .= " GROUP BY v.id ORDER BY v.destacada DESC, v.id DESC"; // Agrupar por ID de vacante
$result = $conn->query($sql);


// Buffer de salida
ob_start();
?>

<div class="container">
    <h1>Lista de Vacantes</h1>

    <!-- Filtro por Departamento -->
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col">
                <select name="departamento" class="form-select">
                    <option value="">Todos los Departamentos</option>
                    <?php while ($row = $result_departamentos->fetch_assoc()) { ?>
                    <option value="<?php echo htmlspecialchars($row['departamento']); ?>" <?php if
                           ($row['departamento'] == $selected_departamento)
                               echo 'selected'; ?>>
                        <?php echo htmlspecialchars($row['departamento']); ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </div>
    </form>

    <?php if (isset($message)) {
        echo "<div class='alert alert-info'>$message</div>";
    } ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Departamento</th>
                <th>Número ID</th>
                <th>Fecha</th>
                <th>Perfil</th>
                <th>Descripción</th>
                <th>Requisitos</th>
                <th>Foto</th>
                <th>Estado</th>
                <th>Tipo</th>
                <th>Seleccionados</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($vacante = $result->fetch_assoc()) { ?>
            <tr class="<?php echo $vacante['destacada'] == '1' ? 'table-success' : ''; ?>">
                <td><?php echo $vacante['id']; ?></td>
                <td><?php echo $vacante['nombre']; ?></td>
                <td><?php echo $vacante['departamento']; ?></td>
                <td><?php echo $vacante['numero_id']; ?></td>
                <td><?php echo $vacante['fecha']; ?></td>
                <td><?php echo $vacante['perfil']; ?></td>
                <td><?php echo $vacante['descripcion']; ?></td>
                <td><?php echo $vacante['requisitos']; ?></td>
                <td><img src="<?php echo $vacante['foto']; ?>" alt="Foto de la vacante" width="100"></td>
                <td><?php echo ucfirst($vacante['estado']); ?></td>
                <td><?php echo ucfirst($vacante['tipo']); ?></td>
                <td>
                    <?php echo $vacante['seleccionados']; ?>/<?php echo $vacante['total_postulantes']; ?>
                    <!-- Mostrar los seleccionados/postulantes -->
                </td>
                <td>
                    <a class="btn btn-primary btn-sm"
                        href="editar_vacantes.php?vacante_id=<?php echo $vacante['id']; ?>">Editar</a>
                    <a class="btn btn-danger btn-sm" href="lista_vacantes.php?delete_id=<?php echo $vacante['id']; ?>"
                        onclick="return confirm('¿Estás seguro de que deseas eliminar esta vacante?');">Eliminar</a>
                    <a class="btn btn-warning btn-sm"
                        href="lista_vacantes.php?update_id=<?php echo $vacante['id']; ?>&current_status=<?php echo $vacante['estado']; ?>">
                        <?php echo $vacante['estado'] == 'abierta' ? 'Cerrar' : 'Abrir'; ?>
                    </a>
                    <a class="btn btn-success btn-sm"
                        href="lista_vacantes.php?highlight_id=<?php echo $vacante['id']; ?>&current_highlight=<?php echo $vacante['destacada']; ?>">
                        <?php echo $vacante['destacada'] == '1' ? 'Quitar Destacar' : 'Destacar'; ?>
                    </a>
                    <a class="btn btn-info btn-sm"
                        href="ver_aplicaciones.php?vacante_id=<?php echo $vacante['id']; ?>">Ver Aplicaciones</a>

                    <!-- Botón para compartir en Twitter -->
                    <a class="btn btn-secondary btn-sm" target="_blank"
                        href="https://twitter.com/intent/tweet?text=<?php echo urlencode('¡Mira esta vacante: ' . $vacante['nombre'] . '!'); ?>&url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>">
                        Compartir en Twitter
                    </a>

                    <a class="btn btn-dark btn-sm"
                        href="lista_vacantes.php?release_id=<?php echo $vacante['id']; ?>&current_type=<?php echo $vacante['tipo']; ?>">
                        <?php echo $vacante['tipo'] == 'interno' ? 'Liberar' : 'Hacer Interna'; ?>
                    </a>

                </td>


            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include 'admin_layout.php';
?>