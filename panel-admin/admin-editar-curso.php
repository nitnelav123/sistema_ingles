<?php
session_start();
$conexion = require("../includes/conexion.php");

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "admin") {
    header("Location: ../login.html");
    exit;
}

$id = $_GET["id"] ?? "";
$sql = "SELECT * FROM Curso WHERE ID_curso = ?";
$sentencia = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($sentencia, "i", $id);
mysqli_stmt_execute($sentencia);
$datos = mysqli_stmt_get_result($sentencia)->fetch_assoc();

if (!$datos) { die("Curso no encontrado."); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar curso - New Ways</title>
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
            <a href="admin-inscripciones.php">Inscripciones</a>
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
        <h2>Editar curso</h2>

        <form id="form-editar-curso" action="admin-actualizar-curso.php" method="POST">
            <input type="hidden" name="id" value="<?= $datos['ID_curso'] ?>">
            <input type="text" name="nombre_curso" value="<?= htmlspecialchars($datos['nombre_curso']) ?>" placeholder="Nombre del curso" required>
            <input type="text" name="descripcion" value="<?= htmlspecialchars($datos['descripcion'] ?? '') ?>" placeholder="Descripción">
            <button type="submit" class="hero-button">Guardar cambios</button>
        </form>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../script.js?v=1.1"></script>
<script>
    manejarEnvioFormulario('form-editar-curso', 'admin-cursos.php', 'Curso actualizado correctamente');
</script>
</body>
</html>