<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../../login.php");
    exit;
}

include '../../db/db.php';

// Obtener el ID de la vacante, el término de búsqueda y los años de experiencia
$vacante_id = isset($_POST['vacante_id']) ? intval($_POST['vacante_id']) : 0;
$search_term = isset($_POST['search_term']) ? trim($_POST['search_term']) : '';
$anos_experiencia = isset($_POST['anos_experiencia']) ? intval($_POST['anos_experiencia']) : -1;

// Filtrar aplicaciones por nombre, carrera y años de experiencia
$filter_condition = '';
if (!empty($search_term)) {
    $search_term_escaped = $conn->real_escape_string($search_term);
    $filter_condition .= " AND (a.nombre LIKE '%$search_term_escaped%' OR a.carrera LIKE '%$search_term_escaped%')";
}

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

// Definir el nombre del archivo CSV
$filename = "aplicaciones_vacante_$vacante_id.csv";

// Configurar encabezados para descarga de CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Crear un archivo CSV en memoria
$output = fopen('php://output', 'w');

// Escribir los encabezados del CSV
fputcsv($output, [
    'Nombre', 'Email', 'Teléfono', 'CV', 'Estado', 'Fecha Entrevista', 'Prueba Técnica', 
    'Calificación', 'Carrera', 'Años de Experiencia', 'Observaciones'
]);

// Escribir los datos de las aplicaciones al CSV
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['nombre'],
        $row['email'],
        $row['telefono'],
        $row['cv'] ? 'Ver CV' : 'No disponible',
        $row['estado'],
        $row['entrevista_datetime'] ? $row['entrevista_datetime'] : 'No programada',
        $row['prueba_tecnica'] ? 'Ver prueba técnica' : 'No subida',
        $row['calificacion_prueba'] !== null ? $row['calificacion_prueba'] : 'No calificada',
        $row['carrera'],
        $row['anos_experiencia'],
        $row['observaciones']
    ]);
}

// Cerrar archivo CSV
fclose($output);

// Cerrar conexión
$conn->close();
exit;
?>
