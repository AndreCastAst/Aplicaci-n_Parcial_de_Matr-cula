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

// CONSULTA SQL CLAVE:
// Usamos JOIN para vincular 3 tablas:
// 1. alumno (a): para obtener el nombre.
// 2. solicitud (sol): para saber qué grado solicitó (sol.grado).
// 3. concepto_pago (cp): para asegurar que SOLO se muestren alumnos que ya pagaron.
// WHERE a.id_seccion IS NULL: Solo mostramos alumnos que aún NO tienen salón.
$sql_alumnos = "
    SELECT a.id_alumno, a.nombre, a.apellido_paterno, a.apellido_materno, sol.grado 
    FROM alumno a
    JOIN solicitud sol ON a.id_alumno = sol.id_alumno
    JOIN concepto_pago cp ON sol.id_solicitud = cp.id_solicitud
    WHERE a.id_seccion IS NULL
";
$result_alumnos = $conn->query($sql_alumnos);

// Obtener secciones disponibles con vacantes
$sql_secciones = "
    SELECT s.id_seccion, s.nombre_seccion, g.id_grado, g.nombre_grado, s.vacantes
    FROM seccion s
    JOIN grado g ON s.id_grado = g.id_grado
    WHERE s.vacantes > 0
    ORDER BY g.nombre_grado, s.nombre_seccion
";
$result_secciones = $conn->query($sql_secciones);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Alumno en Sección</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const alumnoSelect = document.getElementById('id_alumno');
            const seccionSelect = document.getElementById('id_seccion');
            const allSecciones = Array.from(seccionSelect.options);

            alumnoSelect.addEventListener('change', function () {
                // EXPLICACIÓN:
                // 1. Detectamos cuál alumno se seleccionó.
                // 2. Obtenemos su 'data-grado' (atributo personalizado HTML).
                // 3. Filtramos el segundo Select para mostrar solo secciones de ese grado.
                const selectedOption = this.options[this.selectedIndex];
                const gradoAlumno = selectedOption.getAttribute('data-grado');

                // Reiniciar el Select de Secciones (borrar opciones anteriores)
                seccionSelect.innerHTML = '<option value="">Seleccione una sección...</option>';

                if (gradoAlumno) {
                    // LÓGICA DE FILTRADO JS:
                    // 'allSecciones' es una copia de todas las opciones originales.
                    // Usamos .filter() para quedarnos solo con las que coinciden en grado.
                    const filtered = allSecciones.filter(option => option.getAttribute('data-grado') === gradoAlumno);

                    if (filtered.length > 0) {
                        filtered.forEach(opt => seccionSelect.appendChild(opt)); // Agregamos las que pasaron el filtro
                    } else {
                        // Crear opción de aviso si no hay vacantes
                        const opt = document.createElement('option');
                        opt.text = "No hay secciones disponibles para " + gradoAlumno + "° Grado";
                        opt.disabled = true;
                        seccionSelect.appendChild(opt);
                    }
                }
            });

            // Lógica del Modal
            const form = document.querySelector('form');
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Detener la envío por defecto

                const alumnoText = alumnoSelect.options[alumnoSelect.selectedIndex].text;
                const seccionText = seccionSelect.options[seccionSelect.selectedIndex].text;

                document.getElementById('modal-alumno').textContent = alumnoText;
                document.getElementById('modal-seccion').textContent = seccionText;

                document.getElementById('confirmationModal').style.display = 'flex';
            });

            document.getElementById('cancelBtn').addEventListener('click', function () {
                document.getElementById('confirmationModal').style.display = 'none';
            });

            document.getElementById('confirmBtn').addEventListener('click', function () {
                form.submit(); // Enviar el formulario
            });
        });
    </script>
</head>

<body>

    <div class="form-container border-green">
        <h1>Registrar Alumno en Sección</h1>
        <div class="breadcrumb">Matrícula / Asignación / Registrar en Sección</div>

        <!-- Estadísticas para vista rápida -->
        <div class="stats-box">
            <div class="stat-item">
                <div class="stat-value"><?php echo $result_alumnos->num_rows; ?></div>
                <div class="stat-label">Alumnos Aptos</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?php echo $result_secciones->num_rows; ?></div>
                <div class="stat-label">Secciones Disponibles</div>
            </div>
        </div>

        <form action="procesar_inscripcion_seccion.php" method="POST">

            <div class="form-group">
                <label for="id_alumno">Seleccionar Alumno (Con concepto de pago generado)</label>
                <select id="id_alumno" name="id_alumno" class="form-control" required>
                    <option value="">Seleccione un alumno...</option>
                    <?php
                    if ($result_alumnos->num_rows > 0) {
                        while ($row = $result_alumnos->fetch_assoc()) {
                            $id_alumno_escaped = htmlspecialchars($row['id_alumno'], ENT_QUOTES, 'UTF-8');
                            $grado_escaped = htmlspecialchars($row['grado'], ENT_QUOTES, 'UTF-8');
                            $nombre_completo = htmlspecialchars($row['nombre'] . " " . $row['apellido_paterno'], ENT_QUOTES, 'UTF-8');
                            echo "<option value='" . $id_alumno_escaped . "' data-grado='" . $grado_escaped . "'>"
                                . $nombre_completo . " (Grado Sugerido: " . $grado_escaped . "°)"
                                . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No hay alumnos aptos (con pago generado)</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_seccion">Seleccionar Sección</label>
                <select id="id_seccion" name="id_seccion" class="form-control" required>
                    <option value="">Seleccione primero un alumno...</option>
                    <?php
                    if ($result_secciones->num_rows > 0) {
                        while ($row = $result_secciones->fetch_assoc()) {
                            $id_seccion_escaped = htmlspecialchars($row['id_seccion'], ENT_QUOTES, 'UTF-8');
                            $id_grado_escaped = htmlspecialchars($row['id_grado'], ENT_QUOTES, 'UTF-8');
                            $nombre_grado_escaped = htmlspecialchars($row['nombre_grado'], ENT_QUOTES, 'UTF-8');
                            $nombre_seccion_escaped = htmlspecialchars($row['nombre_seccion'], ENT_QUOTES, 'UTF-8');
                            $vacantes_escaped = htmlspecialchars($row['vacantes'], ENT_QUOTES, 'UTF-8');
                            echo "<option value='" . $id_seccion_escaped . "' data-grado='" . $id_grado_escaped . "'>"
                                . $nombre_grado_escaped . " - " . $nombre_seccion_escaped . " (Vacantes: " . $vacantes_escaped . ")"
                                . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn-submit">Confirmar Inscripción</button>

            <div style="margin-top: 20px; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
                <p style="color: #666; margin-bottom: 10px;">¿Necesita cambiar a un alumno de sección?</p>
                <a href="modificar_seccion.php" class="btn-back btn-back-blue">Ir a Modificar Sección</a>
            </div>

            <center><a href="../../index.php" class="btn-back">Volver al menú principal</a></center>
        </form>
    </div>

    <!-- Modal de Confirmación -->
    <div id="confirmationModal" class="modal-overlay">
        <div class="modal">
            <h2>Confirmar Inscripción</h2>
            <p>Por favor verifique los datos antes de continuar.</p>
            <div class="modal-details">
                <p><strong>Alumno:</strong> <span id="modal-alumno"></span></p>
                <p><strong>Sección:</strong> <span id="modal-seccion"></span></p>
            </div>
            <div class="modal-buttons">
                <button id="cancelBtn" class="btn-modal btn-modal-cancel">Cancelar</button>
                <button id="confirmBtn" class="btn-modal btn-modal-confirm">Confirmar</button>
            </div>
        </div>
    </div>

</body>

</html>