<?php
include 'conexion.php';

$sql = "CREATE TABLE IF NOT EXISTS concepto_pago (
    id_concepto INT AUTO_INCREMENT PRIMARY KEY,
    id_solicitud VARCHAR(10) COLLATE utf8mb4_unicode_ci NOT NULL,
    fecha_limite DATE NOT NULL,
    periodo_academico VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    medio_pago VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    descripcion TEXT COLLATE utf8mb4_unicode_ci,
    codigo_pago VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
    fecha_generacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_solicitud) REFERENCES solicitud(id_solicitud) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($conn->query($sql) === TRUE) {
    echo "Tabla 'concepto_pago' creada exitosamente o ya existe.";
} else {
    echo "Error creando tabla: " . $conn->error;
}

$conn->close();
?>
