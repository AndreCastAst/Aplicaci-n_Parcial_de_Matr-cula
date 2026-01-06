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

// Obtener solicitudes pendientes sin concepto generado (opcionalmente verificar si ya existe para filtrar)
// Para simplificar, obteniendo todas las solicitudes de matrícula pendientes.
$sql_solicitudes = "
    SELECT s.id_solicitud, s.id_alumno, a.nombre, a.apellido_paterno, a.apellido_materno, s.grado 
    FROM solicitud s
    JOIN alumno a ON s.id_alumno = a.id_alumno
    LEFT JOIN concepto_pago cp ON s.id_solicitud = cp.id_solicitud
    WHERE s.tipo_solicitud = 'Matricula' AND s.estado = 'Pendiente' AND cp.id_concepto IS NULL
";
$result_solicitudes = $conn->query($sql_solicitudes);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Concepto de Pago</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <script>
        function updateStudentInfo() {
            var select = document.getElementById("id_solicitud");
            var selectedOption = select.options[select.selectedIndex];
            var infoBox = document.getElementById("student-info-display");

            if (selectedOption.value !== "") {
                var name = selectedOption.getAttribute("data-name");
                var grado = selectedOption.getAttribute("data-grado");
                var id = selectedOption.getAttribute("data-id");

                infoBox.innerHTML = `
                    <div class="info-item">ID Alumno: <span>${id}</span></div>
                    <div class="info-item">Nombre: <span>${name}</span></div>
                    <div class="info-item">Grado Solicitado: <span>${grado}°</span></div>
                `;
                infoBox.style.display = "block";
            } else {
                infoBox.style.display = "none";
            }
        }
    </script>
</head>

<body>

    <div class="form-container border-green">
        <h1>Generar Concepto de Pago</h1>
        <div class="breadcrumb">Administración / Pagos / Generar Concepto</div>

        <form action="procesar_concepto.php" method="POST">

            <div class="form-group">
                <label for="id_solicitud">Seleccionar Alumno (Solicitud Pendiente)</label>
                <select id="id_solicitud" name="id_solicitud" class="form-control" onchange="updateStudentInfo()"
                    required>
                    <option value="">Seleccione una solicitud...</option>
                    <?php
                    if ($result_solicitudes->num_rows > 0) {
                        while ($row = $result_solicitudes->fetch_assoc()) {
                            $fullname = htmlspecialchars($row['nombre'] . " " . $row['apellido_paterno'] . " " . $row['apellido_materno'], ENT_QUOTES, 'UTF-8');
                            $id_solicitud_escaped = htmlspecialchars($row['id_solicitud'], ENT_QUOTES, 'UTF-8');
                            $grado_escaped = htmlspecialchars($row['grado'], ENT_QUOTES, 'UTF-8');
                            $id_alumno_escaped = htmlspecialchars($row['id_alumno'], ENT_QUOTES, 'UTF-8');
                            echo "<option value='" . $id_solicitud_escaped . "' 
                                    data-name='" . $fullname . "' 
                                    data-grado='" . $grado_escaped . "'
                                    data-id='" . $id_alumno_escaped . "'>"
                                . $fullname . " (Grado: " . $grado_escaped . "°)</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No hay solicitudes pendientes</option>";
                    }
                    ?>
                </select>
            </div>

            <div id="student-info-display" class="info-box" style="display:none;"></div>

            <div class="grid-layout" style="margin-top: 20px;">
                <div class="form-group">
                    <label for="fecha_limite">Fecha Límite de Pago</label>
                    <input type="date" id="fecha_limite" name="fecha_limite" class="form-control" required>
                </div>
            </div>

            <div class="grid-layout">
                <div class="form-group">
                    <label for="medio_pago">Medio de Pago Permitido</label>
                    <select id="medio_pago" name="medio_pago" class="form-control" required>
                        <option value="Banco">Depósito Bancario (BCP/BBVA)</option>
                        <option value="Efectivo">Pago en Secretaría</option>
                        <option value="Online">Pasarela de Pago Online</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="monto">Monto a pagar (S/.)</label>
                    <input type="number" id="monto" name="monto" class="form-control" value="350.00" step="0.01"
                        required readonly>
                </div>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción del Concepto</label>
                <input type="text" id="descripcion" name="descripcion" class="form-control" value="Matrícula 2025"
                    required>
            </div>

            <div class="warning-box" id="warning-msg">
                <strong>Nota:</strong> Al generar este concepto, se habilitará al estudiante para la asignación de
                sección una vez se confirme el pago (simulado en este paso).
            </div>

            <button type="submit" class="btn-submit">Generar Concepto y Código</button>
            <center><a href="../../index.php" class="btn-back">Volver al Inicio</a></center>
        </form>
    </div>

</body>

</html>