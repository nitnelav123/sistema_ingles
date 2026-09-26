<?php
session_start();
$conexion = require("../includes/conexion.php");

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "admin") {
    header("Location: ../login.html");
    exit;
}

$dni = $_GET["dni"] ?? "";

$sql = "SELECT nombre_persona, apellido_persona, email_persona, telefono_persona, fechaNac_persona
        FROM Persona WHERE DNI_persona = ? AND rol_persona = 'alumno'";
$sentencia = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($sentencia, "s", $dni);
mysqli_stmt_execute($sentencia);
$datos = mysqli_stmt_get_result($sentencia)->fetch_assoc();

if (!$datos) {
    die("Alumno no encontrado.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar alumno - New Ways</title>
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
            <a href="panel-admin.php">Dashboard</a>
            <a href="admin-alumnos.php">Alumnos</a>
            <a href="admin-profesores.php">Profesores</a>
            <a href="admin-cursos.php">Cursos</a>
            <a href="admin-horarios.php">Horarios</a>
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
        <h2>Editar alumno</h2>

        <form id="form-editar-alumno" action="admin-actualizar-alumno.php" method="POST">
            <input type="hidden" name="dni" value="<?= htmlspecialchars($dni) ?>">

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
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../script.js?v=1.1"></script>
<script>
    manejarEnvioFormulario('form-editar-alumno', 'admin-alumnos.php', 'Alumno actualizado correctamente');
</script>
</body>
</html>