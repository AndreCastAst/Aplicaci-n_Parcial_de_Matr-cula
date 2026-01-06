<?php
// Archivo: index.php
session_start();

// EL PORTERO: Si no hay usuario, fuera.
if (!isset($_SESSION['id_usuario'])) {
  header("Location: login.php");
  exit();
}

// CAPTURAMOS EL ROL DEL USUARIO ACTUAL
$rol = $_SESSION['rol'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gestión de Matrículas</title>
  <link rel="stylesheet" href="index.css" />
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
      height: 100vh;
      overflow: hidden;
    }

    .sidebar {
      width: 250px;
      background-color: #2c3e50;
      color: white;
      display: flex;
      flex-direction: column;
      height: 100%;
      padding: 0;
    }

    .sidebar h2 {
      text-align: center;
      padding: 20px 0;
      margin: 0;
      background-color: #1a252f;
    }

    .menu-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .menu-list li a {
      display: block;
      padding: 15px 20px;
      color: #ecf0f1;
      text-decoration: none;
      border-bottom: 1px solid #34495e;
      transition: 0.3s;
    }

    .menu-list li a:hover {
      background-color: #34495e;
    }

    .boton-salir {
      margin-top: auto;
      display: block;
      padding: 15px 20px;
      background-color: #c0392b;
      color: white;
      text-align: center;
      text-decoration: none;
      font-weight: bold;
      transition: 0.3s;
    }

    .boton-salir:hover {
      background-color: #e74c3c;
    }

    .content {
      flex-grow: 1;
      padding: 40px;
      background-color: #f4f6f6;
      overflow-y: auto;
    }
  </style>
</head>

<body>
  <nav class="sidebar">
    <h2>Gestión de Matrículas</h2>

    <div style="text-align: center; padding: 10px; font-size: 12px; color: #bdc3c7;">
      Usuario: <?php echo $_SESSION['usuario']; ?> <br>
      (<?php echo $rol; ?>)
    </div>

    <ul class="menu-list">

      <?php if ($rol == 'secretario') { ?>
        <li>
          <a href="modulos/solicitudes/registrar_solicitud.php">Registrar Solicitud</a>
        </li>
      <?php } ?>


      <?php if ($rol == 'director') { ?>
        <li>
          <a href="modulos/pagos/generar_concepto.php">Generar Concepto de Pago</a>
        </li>
      <?php } ?>


      <?php if ($rol == 'director') { ?>
        <li>
          <a href="modulos/asignacion/registrar_alumno_seccion.php">Registrar alumno en sección</a>
        </li>
      <?php } ?>


      <?php if ($rol == 'director') { ?>
        <li>
          <a href="modulos/asignacion/listar_alumnos.php">Listar Alumnos</a>
        </li>
      <?php } ?>

    </ul>

    <a href="logout.php" class="boton-salir">Cerrar Sesión</a>
  </nav>

  <div class="content">
    <h1>Bienvenido al sistema</h1>
    <p>Hola <b><?php echo $_SESSION['usuario']; ?></b>. Seleccione una opción del menú para comenzar.</p>
  </div>
</body>

</html>