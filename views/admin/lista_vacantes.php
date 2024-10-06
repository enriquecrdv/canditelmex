<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

include '../../db/db.php';

// Paginación
$limit = 5; // Mostrar 5 vacantes por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Obtener todos los departamentos únicos para autocompletar
$sql_departamentos = "SELECT DISTINCT departamento FROM vacantes";
$result_departamentos = $conn->query($sql_departamentos);
$departamentos = [];

while ($row = $result_departamentos->fetch_assoc()) {
    $departamentos[] = $row['departamento'];
}

// Manejar la acción de destacar o quitar destacar
if (isset($_GET['highlight_id'])) {
    $highlight_id = $_GET['highlight_id'];
    $current_highlight = $_GET['current_highlight'];
    $new_highlight = ($current_highlight == '1') ? '0' : '1'; // Cambiar entre destacar y quitar destacar
    
    $sql_highlight = "UPDATE vacantes SET destacada = '$new_highlight' WHERE id = '$highlight_id'";
    
    if ($conn->query($sql_highlight) === TRUE) {
        $message = ($new_highlight == '1') ? "Vacante destacada exitosamente." : "Vacante quitada de destacados.";
    } else {
        $message = "Error al actualizar el estado de destacar: " . $conn->error;
    }

    // Redirigir para evitar que la acción se repita si se refresca la página
    header("Location: lista_vacantes.php");
    exit;
}

// Obtener vacantes filtradas
$selected_departamento = isset($_GET['departamento']) ? $_GET['departamento'] : '';

$sql = "
    SELECT 
        v.*, 
        COUNT(a.id) AS total_postulantes, 
        SUM(CASE WHEN a.estado = 'aprobada' THEN 1 ELSE 0 END) AS seleccionados
    FROM vacantes v
    LEFT JOIN aplicaciones a ON v.id = a.vacante_id
";

if ($selected_departamento != '') {
    $sql .= " WHERE v.departamento LIKE '%$selected_departamento%'";
}

$sql .= " GROUP BY v.id ORDER BY v.destacada DESC, v.id DESC LIMIT $start, $limit"; // Aplicar limit y offset para la paginación
$result = $conn->query($sql);

// Obtener total de vacantes para la paginación
$sql_total = "SELECT COUNT(*) AS total FROM vacantes";
$total_result = $conn->query($sql_total);
$total_vacantes = $total_result->fetch_assoc()['total'];

// Buffer de salida
ob_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Vacantes</title>
    
    <!-- Enlace al archivo CSS lista_vacantes.css -->
    <link rel="stylesheet" href="../../css/lista_vacantes.css">

    <!-- Enlace a Bootstrap si no lo tienes ya -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<div id="vacantes-container" class="container">
    <h1 id="vacantes-titulo">Lista de Vacantes</h1>

    <!-- Filtro por Departamento -->
    <form method="GET" id="vacantes-filtro" class="mb-3">
        <div class="row">
            <div class="col">
                <!-- Campo de búsqueda con autocompletado -->
                <input type="text" name="departamento" id="vacantes-departamento" class="form-control" placeholder="Buscar por Departamento" list="departamentos">
                <datalist id="departamentos">
                    <?php foreach ($departamentos as $departamento) { ?>
                        <option value="<?php echo htmlspecialchars($departamento); ?>"></option>
                    <?php } ?>
                </datalist>
            </div>
            <div class="col-auto">
                <button type="submit" id="vacantes-filtrar-btn" class="btn btn-primary">Filtrar</button>
            </div>
        </div>
    </form>

    <!-- Mostrar mensaje si es necesario -->
    <?php if (isset($message)) { echo "<div class='alert alert-info' id='vacantes-mensaje'>$message</div>"; } ?>

    <!-- Tabla de Vacantes -->
    <table id="vacantes-tabla" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Departamento</th>
                <th>Fecha</th>
                <th>Perfil</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($vacante = $result->fetch_assoc()) { ?>
            <tr class="<?php echo $vacante['destacada'] == '1' ? 'table-success' : ''; ?>">
                <td><?php echo $vacante['id']; ?></td>
                <td><?php echo $vacante['nombre']; ?></td>
                <td><?php echo $vacante['departamento']; ?></td>
                <td><?php echo $vacante['fecha']; ?></td>
                <td><?php echo $vacante['perfil']; ?></td>
                
                <!-- Descripción con opción de expandir -->
                <td>
                    <div id="vacante-<?php echo $vacante['id']; ?>" class="description-content">
                        <div id="vacante-short-<?php echo $vacante['id']; ?>" class="description-short">
                            <?php echo substr($vacante['descripcion'], 0, 50); ?>... <!-- Solo muestra los primeros 50 caracteres -->
                        </div>
                        <div id="vacante-full-<?php echo $vacante['id']; ?>" class="description-full" style="display: none;">
                            <?php echo $vacante['descripcion']; ?> <!-- Descripción completa oculta inicialmente -->
                        </div>
                        <a href="#" id="vacante-toggle-<?php echo $vacante['id']; ?>" class="description-toggle">Expandir</a>
                    </div>
                </td>

                <td><?php echo ucfirst($vacante['estado']); ?></td>
                
                <!-- Acciones como lista desplegable -->
                <td>
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="vacante-dropdown-<?php echo $vacante['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                            Acciones
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="vacante-dropdown-<?php echo $vacante['id']; ?>">
                            <li><a class="dropdown-item" href="editar_vacantes.php?vacante_id=<?php echo $vacante['id']; ?>">Editar</a></li>
                            <li><a class="dropdown-item" href="lista_vacantes.php?delete_id=<?php echo $vacante['id']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta vacante?');">Eliminar</a></li>
                            <li><a class="dropdown-item" href="lista_vacantes.php?update_id=<?php echo $vacante['id']; ?>&current_status=<?php echo $vacante['estado']; ?>"><?php echo $vacante['estado'] == 'abierta' ? 'Cerrar' : 'Abrir'; ?></a></li>
                            <li><a class="dropdown-item" href="lista_vacantes.php?highlight_id=<?php echo $vacante['id']; ?>&current_highlight=<?php echo $vacante['destacada']; ?>"><?php echo $vacante['destacada'] == '1' ? 'Quitar Destacar' : 'Destacar'; ?></a></li>
                            <li><a class="dropdown-item" href="ver_aplicaciones.php?vacante_id=<?php echo $vacante['id']; ?>">Ver Aplicaciones</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Paginación -->
    <nav aria-label="Page navigation" id="vacantes-paginacion">
        <ul class="pagination">
            <?php for ($i = 1; $i <= ceil($total_vacantes / $limit); $i++) { ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php } ?>
        </ul>
    </nav>
</div>

<!-- Incluir JavaScript al final del archivo -->
<script>
    // JavaScript para manejar la expansión de las descripciones
    document.querySelectorAll('.description-toggle').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const content = this.closest('.description-content');
            const shortDescription = content.querySelector('.description-short');
            const fullDescription = content.querySelector('.description-full');
            const toggleButton = this;

            // Verificar si está expandido o minimizado
            if (fullDescription.style.display === 'block') {
                // Minimizar
                fullDescription.style.display = 'none';
                shortDescription.style.display = 'block';
                toggleButton.textContent = 'Expandir'; // Cambiar el texto del botón
            } else {
                // Expandir
                fullDescription.style.display = 'block';
                shortDescription.style.display = 'none';
                toggleButton.textContent = 'Minimizar'; // Cambiar el texto del botón
            }
        });
    });
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
$content = ob_get_clean();
include 'admin_layout.php';
?>
