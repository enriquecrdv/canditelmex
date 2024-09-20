<?php
session_start();
if ($_SESSION['role'] != 'user') {
    header("Location: ../../login.php");
    exit;
}

include '../../db/db.php';

// Obtener el tipo_candidato del usuario actual
$user_id = $_SESSION['id']; // Asegúrate de tener el ID del usuario en la sesión
$sql_tipo_candidato = "SELECT tipo_candidato FROM users WHERE id = ?";
$stmt_tipo_candidato = $conn->prepare($sql_tipo_candidato);
$stmt_tipo_candidato->bind_param('i', $user_id);
$stmt_tipo_candidato->execute();
$result_tipo_candidato = $stmt_tipo_candidato->get_result();
$tipo_candidato = $result_tipo_candidato->fetch_assoc()['tipo_candidato'];

// Obtener todos los departamentos únicos
$sql_departamentos = "SELECT DISTINCT departamento FROM vacantes";
$result_departamentos = $conn->query($sql_departamentos);

// Obtener los filtros de búsqueda
$search_title = isset($_GET['title']) ? $_GET['title'] : '';
$search_departamento = isset($_GET['departamento']) ? $_GET['departamento'] : '';

// Consulta para obtener las vacantes filtradas
$sql = "SELECT * FROM vacantes WHERE estado = 'abierta'"; // Excluir vacantes cerradas

// Filtrar vacantes según el tipo_candidato del usuario
if ($tipo_candidato == 'externo') {
    $sql .= " AND tipo = 'externo'"; // Solo vacantes externas para candidatos externos
}

// Agregar filtros de búsqueda por título y departamento
if ($search_title != '') {
    $sql .= " AND nombre LIKE ?";
}
if ($search_departamento != '') {
    $sql .= " AND departamento = ?";
}

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($sql);

// Vincular parámetros de búsqueda
if ($search_title != '' && $search_departamento != '') {
    $search_title = "%$search_title%";
    $stmt->bind_param('ss', $search_title, $search_departamento);
} elseif ($search_title != '') {
    $search_title = "%$search_title%";
    $stmt->bind_param('s', $search_title);
} elseif ($search_departamento != '') {
    $stmt->bind_param('s', $search_departamento);
}

$stmt->execute();
$result = $stmt->get_result();

// Buffer de salida
ob_start();
?>

<div class="container">
    <h1>Buscar Vacantes</h1>
    
    <!-- Formulario de Búsqueda -->
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col">
                <input type="text" name="title" class="form-control" placeholder="Buscar por título" value="<?php echo htmlspecialchars($search_title); ?>">
            </div>
            <div class="col">
                <select name="departamento" class="form-select">
                    <option value="">Todos los Departamentos</option>
                    <?php while ($row = $result_departamentos->fetch_assoc()) { ?>
                        <option value="<?php echo htmlspecialchars($row['departamento']); ?>"
                            <?php if ($row['departamento'] == $search_departamento) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($row['departamento']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </div>
    </form>
    
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
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($vacante = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($vacante['id']); ?></td>
                <td><?php echo htmlspecialchars($vacante['nombre']); ?></td>
                <td><?php echo htmlspecialchars($vacante['departamento']); ?></td>
                <td><?php echo htmlspecialchars($vacante['numero_id']); ?></td>
                <td><?php echo htmlspecialchars($vacante['fecha']); ?></td>
                <td><?php echo htmlspecialchars($vacante['perfil']); ?></td>
                <td><?php echo htmlspecialchars($vacante['descripcion']); ?></td>
                <td><?php echo htmlspecialchars($vacante['requisitos']); ?></td>
                <td><img src="<?php echo htmlspecialchars($vacante['foto']); ?>" alt="Foto de la vacante" width="100"></td>
                <td>
                    <a class="btn btn-success btn-sm" href="aplicar_vacante.php?vacante_id=<?php echo htmlspecialchars($vacante['id']); ?>">Aplicar</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include 'user_layout.php'; // Utiliza un layout específico para los usuarios, si existe
?>
