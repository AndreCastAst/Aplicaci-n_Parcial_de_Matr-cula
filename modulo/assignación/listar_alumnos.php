<?php
session_start();

// 1. Verificar si está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login.php");
    exit();
}

// 2. VERIFICAR SI ES DIRECTOR
if ($_SESSION['rol'] != 'director') { // Asegúrate que coincida mayúscula/minúscula con tu BD
    echo "<h1>Acceso Denegado</h1><p>No tienes permiso para ver esta página.</p>";
    echo "<a href='../../index.php'>Volver</a>";
    exit(); 
}

// Incluye el archivo de conexión.
include '../../conexion.php'; 

// Verificar conexión
if (!isset($conn) || $conn->connect_error) {
    die("Error al incluir la conexión o la conexión a la base de datos es nula.");
}

$alumnos_agrupados = [];
$grados_disponibles = [];
$secciones_disponibles = [];

// Obtener grados
$sql_grados = "SELECT id_grado, nombre_grado FROM grado ORDER BY id_grado";
$result_grados = $conn->query($sql_grados);
if ($result_grados && $result_grados->num_rows > 0) {
    while($row = $result_grados->fetch_assoc()) {
        $grados_disponibles[] = $row;
    }
}

// Obtener secciones
$sql_secciones = "SELECT id_seccion, nombre_seccion, id_grado FROM seccion ORDER BY id_grado, nombre_seccion";
$result_secciones = $conn->query($sql_secciones);
if ($result_secciones && $result_secciones->num_rows > 0) {
    while($row = $result_secciones->fetch_assoc()) {
        $secciones_disponibles[$row['id_grado']][] = $row;
    }
}

// Filtros
$filtro_grado_id = isset($_GET['grado']) ? $_GET['grado'] : '';
$filtro_seccion_id = isset($_GET['seccion']) ? $_GET['seccion'] : '';
$nombre_grado_seleccionado = '';
$nombre_seccion_seleccionada = '';

if (!empty($filtro_grado_id)) {
    $sql_alumnos = "
        SELECT a.nombre, a.apellido_paterno, a.apellido_materno,
               g.nombre_grado AS grado, s.nombre_seccion AS seccion,
               g.id_grado, s.id_seccion
        FROM alumno a
        LEFT JOIN seccion s ON a.id_seccion = s.id_seccion
        LEFT JOIN grado g ON s.id_grado = g.id_grado
        WHERE g.id_grado = ?
    ";
    
    $params = [$filtro_grado_id];
    $types = 's';
    
    if (!empty($filtro_seccion_id)) {
        $sql_alumnos .= " AND s.id_seccion = ?";
        $params[] = $filtro_seccion_id;
        $types .= 's';
    }

    $sql_alumnos .= " ORDER BY s.nombre_seccion, a.apellido_paterno, a.apellido_materno";

    if ($stmt = $conn->prepare($sql_alumnos)) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result_alumnos = $stmt->get_result();

        if ($result_alumnos->num_rows > 0) {
            while($row = $result_alumnos->fetch_assoc()) {
                $alumnos_agrupados[] = $row;
                $nombre_grado_seleccionado = $row['grado'];
                if (!empty($filtro_seccion_id)) {
                    $nombre_seccion_seleccionada = $row['seccion'];
                }
            }
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Listado de Alumnos</title>
    <link rel="stylesheet" href="../../index.css" /> 
    <style>
        /* REPETIMOS LOS ESTILOS DEL LAYOUT PARA QUE NO SE ROMPA */
        body { margin: 0; font-family: Arial, sans-serif; display: flex; height: 100vh; overflow: hidden; }
        
        /* Estilos del Sidebar */
        .sidebar { width: 250px; background-color: #2c3e50; color: white; display: flex; flex-direction: column; height: 100%; padding: 0; flex-shrink: 0; }
        .sidebar h2 { text-align: center; padding: 20px 0; margin: 0; background-color: #1a252f; font-size: 1.2em; border: none; }
        .menu-list { list-style: none; padding: 0; margin: 0; }
        .menu-list li a { display: block; padding: 15px 20px; color: #ecf0f1; text-decoration: none; border-bottom: 1px solid #34495e; transition: 0.3s; }
        .menu-list li a:hover { background-color: #34495e; }
        .boton-salir { margin-top: auto; display: block; padding: 15px 20px; background-color: #c0392b; color: white; text-align: center; text-decoration: none; font-weight: bold; transition: 0.3s; }
        .boton-salir:hover { background-color: #e74c3c; }

        /* Estilos del contenido */
        .content { flex-grow: 1; padding: 40px; background-color: #f4f6f6; overflow-y: auto; }

        /* Estilos específicos del formulario de esta página */
        .filter-form { background-color: #ecf0f1; padding: 15px; border-radius: 5px; margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-end; }
        .filter-form label { display: block; font-weight: bold; margin-bottom: 5px; }
        .filter-form select { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        
        .alumnos-table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: white; }
        .alumnos-table th, .alumnos-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .alumnos-table th { background-color: #3498db; color: white; }
        
        h1 { margin-top: 0; }
    </style>
  </head>

  <body>
    <nav class="sidebar">
      <h2>Gestión de Matrículas</h2>
      <ul class="menu-list">
        <li><a href="../pagos/generar_concepto.php">Generar Concepto de Pago</a></li>
        <li><a href="registrar_alumno_seccion.php">Registrar alumno en sección</a></li>
        <li><a href="listar_alumnos.php" style="background-color: #34495e;">Listar Alumnos</a></li>
      </ul>
      
      <a href="../../logout.php" class="boton-salir">Cerrar Sesión</a>
    </nav>

    <div class="content">
      <h1>Listado de Alumnos por Grado y Sección</h1>

      <form method="GET" action="listar_alumnos.php" class="filter-form" id="filterForm">
        <div>
            <label for="grado">Grado:</label>
            <select name="grado" id="grado" required>
                <option value="">-- Seleccione Grado --</option>
                <?php foreach ($grados_disponibles as $grado): ?>
                    <option 
                        value="<?php echo htmlspecialchars($grado['id_grado']); ?>"
                        <?php echo ($filtro_grado_id == $grado['id_grado']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($grado['nombre_grado']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label for="seccion">Sección (Opcional):</label>
            <select name="seccion" id="seccion">
                <option value="">-- Todas las Secciones --</option>
                <?php 
                if (isset($secciones_disponibles[$filtro_grado_id])): 
                    foreach ($secciones_disponibles[$filtro_grado_id] as $seccion): ?>
                        <option 
                            value="<?php echo htmlspecialchars($seccion['id_seccion']); ?>"
                            <?php echo ($filtro_seccion_id == $seccion['id_seccion']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($seccion['nombre_seccion']); ?>
                        </option>
                    <?php endforeach; 
                endif; ?>
            </select>
        </div>
        <div style="padding-bottom: 2px;">
             <a href="../../index.php" style="text-decoration: none; color: #7f8c8d; font-size: 0.9em;">Volver al Inicio</a>
        </div>
      </form>
      
      <hr>

      <?php if (!empty($filtro_grado_id)): ?>
          <h2>
              Alumnos en 
              <?php echo htmlspecialchars($nombre_grado_seleccionado); ?> 
              <?php echo !empty($filtro_seccion_id) ? "(" . htmlspecialchars($nombre_seccion_seleccionada) . ")" : ""; ?>
              (Total: <?php echo count($alumnos_agrupados); ?>)
          </h2>

          <?php if (count($alumnos_agrupados) > 0): ?>
            <table class="alumnos-table">
              <thead>
                <tr>
                  <th>Nombre del Alumno</th>
                  <th>Grado</th>
                  <th>Sección</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($alumnos_agrupados as $alumno): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellido_paterno'] . ' ' . $alumno['apellido_materno']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['grado']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['seccion']); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p style="padding: 20px; background: white; border-radius: 5px;">No se encontraron alumnos asignados a los criterios seleccionados.</p>
          <?php endif; ?>
      <?php else: ?>
          <p>Seleccione un Grado para visualizar la lista de alumnos.</p>
      <?php endif; ?>
    </div>
    
    <script>
        document.getElementById('grado').addEventListener('change', function() {
            document.getElementById('seccion').value = ''; 
            document.getElementById('filterForm').submit();
        });

        document.getElementById('seccion').addEventListener('change', function() {
            if (document.getElementById('grado').value !== '') {
                document.getElementById('filterForm').submit();
            }
        });
    </script>
  </body>
</html>