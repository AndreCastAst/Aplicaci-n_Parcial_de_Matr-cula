<?php
session_start();

// 1. Verificar si está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login.php");
    exit();
}

// 2. VERIFICAR SI ES DIRECTOR
if ($_SESSION['rol'] != 'director') {
    // Si NO es director, lo mandamos al inicio y le prohibimos entrar
    echo "<h1>Acceso Denegado</h1><p>No tienes permiso para ver esta página.</p>";
    echo "<a href='../../index.php'>Volver</a>";
    exit(); // Detenemos la carga de la página
}
include '../../conexion.php';

$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$student_data = null;
$current_sections = [];

if ($search_term) {
    // Sanitizar término de búsqueda
    $search_term = trim($search_term);
    
    // Buscar alumno que YA tiene sección asignada usando prepared statements
    $sql_search = "
        SELECT a.id_alumno, a.nombre, a.apellido_paterno, a.apellido_materno, a.id_seccion, s.nombre_seccion, s.id_grado
        FROM alumno a
        JOIN seccion s ON a.id_seccion = s.id_seccion
        WHERE (a.nombre LIKE ? OR a.id_alumno LIKE ?)
        LIMIT 1
    ";
    $stmt_search = $conn->prepare($sql_search);
    $search_pattern = "%" . $search_term . "%";
    $stmt_search->bind_param("ss", $search_pattern, $search_pattern);
    $stmt_search->execute();
    $result_search = $stmt_search->get_result();
    
    if ($result_search->num_rows > 0) {
        $student_data = $result_search->fetch_assoc();
        
        // Cargar secciones disponibles DEL MISMO GRADO, excluyendo la actual usando prepared statements
        $grado_id = $student_data['id_grado'];
        $current_sec_id = $student_data['id_seccion'];
        
        $sql_sections = "
            SELECT id_seccion, nombre_seccion, vacantes
            FROM seccion 
            WHERE id_grado = ? 
            AND id_seccion != ?
            AND vacantes > 0
        ";
        $stmt_sections = $conn->prepare($sql_sections);
        $stmt_sections->bind_param("ss", $grado_id, $current_sec_id);
        $stmt_sections->execute();
        $current_sections = $stmt_sections->get_result();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Sección</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <script>
        function confirmChange(e) {
            e.preventDefault();
            document.getElementById('confirmationModal').style.display = 'flex';
        }
        function submitForm() {
            document.getElementById('changeForm').submit();
        }
        function closeModal() {
            document.getElementById('confirmationModal').style.display = 'none';
        }
    </script>
</head>
<body>

    <div class="form-container border-blue">
        <h1>Modificar Sección de Alumno</h1>
        <div class="breadcrumb">Matrícula / Asignación / Modificar Sección</div>

        <!-- 1. Buscar -->
        <form action="" method="GET" class="search-box">
            <input type="text" name="search" class="search-input" placeholder="Buscar por Nombre o DNI..." value="<?php echo htmlspecialchars($search_term); ?>" required>
            <button type="submit" class="btn-search">Buscar Alumno</button>
        </form>

        <?php if ($search_term && !$student_data): ?>
            <p style="color: red; text-align: center;">No se encontró ningún alumno asignado a una sección con ese criterio.</p>
        <?php endif; ?>

        <?php if ($student_data): ?>
            <!-- 2. Mostrar Información & Formulario -->
            <div class="student-info">
                <h3>Datos del Alumno</h3>
                <div class="info-row">
                    <span class="info-label">Nombre:</span>
                    <span><?php echo htmlspecialchars($student_data['nombre'] . ' ' . $student_data['apellido_paterno'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Sección Actual:</span>
                    <span><?php echo htmlspecialchars($student_data['nombre_seccion'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </div>

            <form id="changeForm" action="procesar_cambio_seccion.php" method="POST" onsubmit="confirmChange(event)">
                <input type="hidden" name="id_alumno" value="<?php echo htmlspecialchars($student_data['id_alumno'], ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="old_section_id" value="<?php echo htmlspecialchars($student_data['id_seccion'], ENT_QUOTES, 'UTF-8'); ?>">

                <div class="form-group">
                    <label style="font-weight: bold; display: block; margin-bottom: 8px;">Seleccionar Nueva Sección (Mismo Grado)</label>
                    <select name="new_section_id" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <?php
                        if ($current_sections && $current_sections->num_rows > 0) {
                            while($row = $current_sections->fetch_assoc()) {
                                $id_seccion_escaped = htmlspecialchars($row['id_seccion'], ENT_QUOTES, 'UTF-8');
                                $nombre_seccion_escaped = htmlspecialchars($row['nombre_seccion'], ENT_QUOTES, 'UTF-8');
                                $vacantes_escaped = htmlspecialchars($row['vacantes'], ENT_QUOTES, 'UTF-8');
                                echo "<option value='" . $id_seccion_escaped . "'>" . $nombre_seccion_escaped . " (Vacantes: " . $vacantes_escaped . ")</option>";
                            }
                        } else {
                            echo "<option value='' disabled>No hay otras secciones disponibles</option>";
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn-submit" <?php if (!$current_sections || $current_sections->num_rows == 0) echo 'disabled'; ?>>Guardar Cambios</button>
            </form>
            
            <div style="margin-top: 30px; border-top: 1px solid #ccc; padding-top: 20px; text-align: right;">
                <form id="deleteForm" action="procesar_retiro_seccion.php" method="POST" onsubmit="confirmDelete(event)">
                    <input type="hidden" name="id_alumno" value="<?php echo htmlspecialchars($student_data['id_alumno'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="id_seccion" value="<?php echo htmlspecialchars($student_data['id_seccion'], ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="btn-red">Retirar Alumno de Sección</button>
                </form>
            </div>

        <?php endif; ?>
        
        <center><a href="registrar_alumno_seccion.php" class="btn-back">Volver</a></center>
    </div>

    <!-- Modal Cambio -->
    <div id="confirmationModal" class="modal-overlay">
        <div class="modal">
            <h2>Confirmar Cambio</h2>
            <p>¿Está seguro de cambiar la sección de este alumno?</p>
            <div class="modal-buttons">
                <button class="btn-modal btn-modal-cancel" onclick="closeModal()">Cancelar</button>
                <button class="btn-modal btn-modal-confirm" onclick="submitForm()">Confirmar</button>
            </div>
        </div>
    </div>

    <!-- Modal Retiro -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal">
            <h2 style="color: #dc3545;">⚠ Confirmar Retiro</h2>
            <p>¿Está seguro de retirar al alumno de la sección actual?</p>
            <p style="font-size: 0.9em; color: #666;">El alumno quedará sin sección asignada y podrá ser registrado nuevamente.</p>
            <div class="modal-buttons">
                <button class="btn-modal btn-modal-cancel" onclick="closeDeleteModal()">Cancelar</button>
                <button class="btn-modal btn-modal-confirm" style="background-color: #dc3545;" onclick="submitDeleteForm()">Sí, Retirar</button>
            </div>
        </div>
    </div>

    <script>
        function confirmChange(e) {
            e.preventDefault();
            document.getElementById('confirmationModal').style.display = 'flex';
        }
        function submitForm() {
            document.getElementById('changeForm').submit();
        }
        function closeModal() {
            document.getElementById('confirmationModal').style.display = 'none';
        }

        // Retiro 
        function confirmDelete(e) {
            e.preventDefault();
            document.getElementById('deleteModal').style.display = 'flex';
        }
        function submitDeleteForm() {
            document.getElementById('deleteForm').submit();
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
    </script>
