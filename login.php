<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema - Colegio</title>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f2f5;
            margin: 0;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 320px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            background-color: #ffebe6;
            color: #d93025;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
            border: 1px solid #ffbdad;
        }

        #logo {
            position: absolute;
            top: 80px;
            right: center;
        }
    </style>
</head>

<body>
    <img src="logo.png" alt="Logo" id="logo">

    <div class="login-container">
        <h2>Iniciar Sesión</h2>

        <?php if (isset($_GET['error'])): ?>
            <div class="error">Usuario o contraseña incorrectos</div>
        <?php endif; ?>

        <form action="validar.php" method="POST">
            <input type="text" name="user" required placeholder="Usuario" autofocus>
            <input type="password" name="pass" required placeholder="Contraseña">
            <button type="submit">Ingresar</button>
        </form>
    </div>

</body>

</html>