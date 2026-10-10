<?php
    session_start();

    $conexion = require("../includes/conexion.php");

    if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "alumno") {
    header("Location: ../login.html");
    exit;
    }

    $sql_perfil = "SELECT nombre_persona, apellido_persona, email_persona, telefono_persona, fechaNac_persona
                   FROM Persona WHERE DNI_persona = ?";
    $sentencia_perfil = mysqli_prepare($conexion, $sql_perfil);
    mysqli_stmt_bind_param($sentencia_perfil, "s", $_SESSION["dni"]);
    mysqli_stmt_execute($sentencia_perfil);
    $datos = mysqli_stmt_get_result($sentencia_perfil)->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil - New Ways</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link rel="stylesheet" href="../style.css?v=1.1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<header class="header">
    <div class="header-container">
        <div class="header-left">
            <img src="../img/Logo.png" class="logo">
            <h1>New ways</h1>
        </div>
        <nav class="menu">
            <a href="panel-alumno.php">Inicio</a>
            <a href="mi-curso.php">Mi curso</a>
            <a href="perfil.php">Perfil</a>
            <a href="../logout.php">Cerrar sesión</a>
        </nav>

        <div class="toggle-container">
            <input type="checkbox" id="modoToggle">
            <label for="modoToggle" class="toggle"></label>
        </div>
    </div>
</header>

<section class="form-section">
    <div class="form-card">
        <h2>Mi perfil</h2>

        <form id="form-perfil" action="../modificar.php" method="POST"
            data-ajax
            data-pregunta="¿Guardar los cambios?"
            data-exito="Datos actualizados correctamente">
            
            <div class="form-row">
                <input type="text" name="nombre_persona" value="<?= htmlspecialchars($datos['nombre_persona']) ?>" required>
                <input type="text" name="apellido_persona" value="<?= htmlspecialchars($datos['apellido_persona']) ?>" required>
            </div>

            <input type="email" name="email_persona" value="<?= htmlspecialchars($datos['email_persona']) ?>" required>

            <input type="tel" name="telefono_persona" value="<?= htmlspecialchars($datos['telefono_persona']) ?>" required>

            <label class="form-date-label">
                Fecha de nacimiento
                <input type="date" name="fechaNac_persona" value="<?= htmlspecialchars($datos['fechaNac_persona']) ?>" required>
            </label>

            <button type="submit" class="hero-button">Guardar cambios</button>

        </form>    
    
        <hr style="margin: 30px 0; border-color: var(--borde);">

        <h3 style="color: var(--azul); margin-bottom: 15px;">Seguridad</h3>
        <a href="../cambiar-password.php" class="hero-button" style="display:inline-block; text-decoration:none;">Cambiar contraseña</a>
    </div>
</section>


<script src="../script.js?v=1.2"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>