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

// Ayudante para generar código de pago aleatorio
function generatePaymentCode($length = 10) {
    return substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar y sanitizar datos de entrada
    $id_solicitud = isset($_POST['id_solicitud']) ? trim($_POST['id_solicitud']) : '';
    $fecha_limite = isset($_POST['fecha_limite']) ? trim($_POST['fecha_limite']) : '';
    // Periodo retirado del form, asignamos valor por defecto (Año actual + I)
    $periodo = isset($_POST['periodo_academico']) ? trim($_POST['periodo_academico']) : date("Y") . "-1";
    $medio = isset($_POST['medio_pago']) ? trim($_POST['medio_pago']) : '';
    $monto = isset($_POST['monto']) ? floatval($_POST['monto']) : 0;
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';

    // Validaciones básicas
    if (empty($id_solicitud) || empty($fecha_limite) || empty($medio) || $monto <= 0 || empty($descripcion)) {
        $status = "error";
        $message = "Error: Faltan datos requeridos o datos inválidos.";
    } else {
        // Validar que el medio de pago sea uno de los permitidos
        $medios_permitidos = ['Banco', 'Efectivo', 'Online'];
        if (!in_array($medio, $medios_permitidos)) {
            $status = "error";
            $message = "Error: Medio de pago no válido.";
        } else {
            // Generar código único
            $codigo_pago = "CP-" . generatePaymentCode(8);
            $fecha_generacion = date("Y-m-d");

            // Usar prepared statements para prevenir SQL Injection
            $sql = "INSERT INTO concepto_pago (id_solicitud, fecha_limite, periodo_academico, medio_pago, monto, descripcion, codigo_pago, fecha_generacion)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ssssdsss", $id_solicitud, $fecha_limite, $periodo, $medio, $monto, $descripcion, $codigo_pago, $fecha_generacion);
                
                if ($stmt->execute()) {
                    $status = "success";
                    $message = "Concepto Generado Exitosamente";
                } else {
                    $status = "error";
                    $message = "Error al generar concepto: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $status = "error";
                $message = "Error al preparar la consulta: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado Concepto de Pago</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
    <div class="result-box">
        <?php if ($status == 'success'): ?>
            <div class="success">✅ <?php echo $message; ?></div>
            
            <p>Se ha generado el siguiente código de pago único:</p>
            <div class="code-display"><?php echo htmlspecialchars($codigo_pago, ENT_QUOTES, 'UTF-8'); ?></div>
            
            <p style="color: #666; font-size: 0.9em;">Entregue este código al padre de familia para realizar el pago en los canales autorizados.</p>
            
            <button onclick="window.print()" class="btn btn-blue" style="margin-top: 20px;">🖨️ Imprimir Comprobante</button>

        <?php else: ?>
            <div class="error">❌ <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        
        <br>
        <a href="generar_concepto.php" class="btn btn-gray" style="margin-top: 20px;">Generar Nuevo</a>
        <a href="../../index.php" class="btn btn-back-blue" style="display:block; margin-top:10px; width: fit-content; margin-left: auto; margin-right: auto;">Volver al Menú</a>
    </div>
</body>
</html>
