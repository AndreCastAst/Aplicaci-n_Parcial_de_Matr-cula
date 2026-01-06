<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            padding: 30px;
        }

        .registro-form-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            border: 2px solid #333;
        }

        fieldset {
            border: 2px solid #ccc;
            padding: 25px;
            margin-bottom: 30px;
            border-radius: 5px;
        }

        legend {
            font-weight: bold;
            color: #555;
            padding: 0 10px;
            font-size: 1.1em;
        }

        .form-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .label-box {
            background-color: #ccc;
            color: #333;
            padding: 10px;
            font-weight: bold;
            width: 200px;
            display: flex;
            align-items: center;
            border-radius: 4px;
            margin-right: 20px;
            font-size: 0.9em;
            text-transform: uppercase;
        }

        .input-container {
            flex-grow: 1;
            display: flex;
            align-items: center;
        }

        .input-field {
            width: 100%;
            padding: 10px;
            border: 2px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
            outline: none;
            transition: border-color 0.3s;
            background-color: white;
        }

        .input-field:focus {
            border-color: #2c80d3;
        }

        .required-marker {
            color: red;
            font-size: 1.5em;
            margin-left: 10px;
            font-weight: bold;
        }

        .form-actions {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        .btn-registrar {
            background-color: #2c80d3;
            color: white;
            border: none;
            padding: 12px 50px;
            font-size: 1.1em;
            font-weight: bold;
            border-radius: 30px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s;
        }

        .btn-registrar:hover {
            background-color: #1a5b9e;
        }

        .volverInicio {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 0.95em;
            color: #2c80d3;
            transition: color 0.3s;

        }
    </style>
</head>

<body>

    <div class="registro-form-container">
        <form id="formRegistro" action="registrar.php" method="POST">

            <fieldset>
                <legend>DATOS DEL ESTUDIANTE</legend>

                <div class="form-row">
                    <label class="label-box" for="est_nombres">NOMBRES</label>
                    <div class="input-container">
                        <input type="text" id="est_nombres" name="est_nombres" class="input-field" required>
                        <span class="required-marker">*</span>
                    </div>
                </div>

                <div class="form-row">
                    <label class="label-box" for="est_ape_paterno">APELLIDO PATERNO</label>
                    <div class="input-container">
                        <input type="text" id="est_ape_paterno" name="est_ape_paterno" class="input-field" required>
                        <span class="required-marker">*</span>
                    </div>
                </div>

                <div class="form-row">
                    <label class="label-box" for="est_ape_materno">APELLIDO MATERNO</label>
                    <div class="input-container">
                        <input type="text" id="est_ape_materno" name="est_ape_materno" class="input-field" required>
                        <span class="required-marker">*</span>
                    </div>
                </div>

                <div class="form-row">
                    <label class="label-box" for="est_dni">DNI</label>
                    <div class="input-container">
                        <input type="text" id="est_dni" name="est_dni" class="input-field" maxlength="8"
                            pattern="[0-9]{8}" required>
                        <span class="required-marker">*</span>
                    </div>
                </div>

                <div class="form-row">
                    <label class="label-box" for="est_fecha_nac">FECHA NACIMIENTO</label>
                    <div class="input-container">
                        <input type="date" id="est_fecha_nac" name="est_fecha_nac" class="input-field" required>
                        <span class="required-marker">*</span>
                    </div>
                </div>

                <div class="form-row">
                    <label class="label-box" for="est_grado">GRADO</label>
                    <div class="input-container">
                        <select id="est_grado" name="est_grado" class="input-field" required>
                            <option value="">Seleccione...</option>
                            <option value="1">1° Grado</option>
                            <option value="2">2° Grado</option>
                            <option value="3">3° Grado</option>
                            <option value="4">4° Grado</option>
                            <option value="5">5° Grado</option>
                            <option value="6">6° Grado</option>
                        </select>
                        <span class="required-marker">*</span>
                    </div>
                </div>
            </fieldset>


            <fieldset>
                <legend>DATOS DEL APODERADO</legend>

                <div class="form-row">
                    <label class="label-box" for="apo_nombres">NOMBRES</label>
                    <div class="input-container">
                        <input type="text" id="apo_nombres" name="apo_nombres" class="input-field" required>
                        <span class="required-marker">*</span>
                    </div>
                </div>

                <div class="form-row">
                    <label class="label-box" for="apo_ape_paterno">APELLIDO PATERNO</label>
                    <div class="input-container">
                        <input type="text" id="apo_ape_paterno" name="apo_ape_paterno" class="input-field" required>
                        <span class="required-marker">*</span>
                    </div>
                </div>

                <div class="form-row">
                    <label class="label-box" for="apo_ape_materno">APELLIDO MATERNO</label>
                    <div class="input-container">
                        <input type="text" id="apo_ape_materno" name="apo_ape_materno" class="input-field" required>
                        <span class="required-marker">*</span>
                    </div>
                </div>

                <div class="form-row">
                    <label class="label-box" for="apo_dni">DNI</label>
                    <div class="input-container">
                        <input type="text" id="apo_dni" name="apo_dni" class="input-field" maxlength="8"
                            pattern="[0-9]{8}" required>
                        <span class="required-marker">*</span>
                    </div>
                </div>

                <div class="form-row">
                    <label class="label-box" for="apo_telefono">TELÉFONO</label>
                    <div class="input-container">
                        <input type="tel" id="apo_telefono" name="apo_telefono" class="input-field" maxlength="10"
                            required>
                        <span class="required-marker">*</span>
                    </div>
                </div>

                <div class="form-row">
                    <label class="label-box" for="apo_email">EMAIL</label>
                    <div class="input-container">
                        <input type="email" id="apo_email" name="apo_email" class="input-field" required>
                        <span class="required-marker">*</span>
                    </div>
                </div>

            </fieldset>

            <div class="form-actions">
                <button type="submit" class="btn-registrar">Registrar</button>
            </div>
            <div class="Volver Inicio">
                <a href="../../index.php" class="volverInicio">Volver al Inicio</a>
            </div>
        </form>
    </div>
</body>

</html>