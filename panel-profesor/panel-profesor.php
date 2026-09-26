<?php
session_start();
$conexion = require("../includes/conexion.php");

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "profesor") {
    header("Location: ../login.html");
    exit;
}

$sql_id = "SELECT ID_profesor FROM Profesores WHERE DNI_persona = ?";
$sentencia_id = mysqli_prepare($conexion, $sql_id);
mysqli_stmt_bind_param($sentencia_id, "s", $_SESSION["dni"]);
mysqli_stmt_execute($sentencia_id);
$id_profesor = mysqli_stmt_get_result($sentencia_id)->fetch_assoc()["ID_profesor"];

// Contador de cursos asignados
$sql_cursos = "SELECT COUNT(DISTINCT ID_curso) AS total FROM Horarios WHERE ID_profesor = ?";
$sentencia_cursos = mysqli_prepare($conexion, $sql_cursos);
mysqli_stmt_bind_param($sentencia_cursos, "i", $id_profesor);
mysqli_stmt_execute($sentencia_cursos);
$total_cursos = mysqli_stmt_get_result($sentencia_cursos)->fetch_assoc()["total"];

// Contador de alumnos (inscripciones activas en sus horarios)
$sql_alumnos = "SELECT COUNT(DISTINCT i.ID_cliente) AS total
                FROM Inscripcion i
                JOIN Horarios h ON h.ID_horarios = i.ID_horarios
                WHERE h.ID_profesor = ? AND i.estado = 'activa'";
$sentencia_alumnos = mysqli_prepare($conexion, $sql_alumnos);
mysqli_stmt_bind_param($sentencia_alumnos, "i", $id_profesor);
mysqli_stmt_execute($sentencia_alumnos);
$total_alumnos = mysqli_stmt_get_result($sentencia_alumnos)->fetch_assoc()["total"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del profesor - New Ways</title>
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
            <a href="panel-profesor.php">Inicio</a>
            <a href="mis-cursos.php">Mis cursos</a>
            <a href="mis-alumnos.php">Mis alumnos</a>
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
        <h2>Hola, <?= htmlspecialchars($_SESSION["nombre"]) ?> 👋</h2>
        <p>Tenés <?= $total_cursos ?> curso<?= $total_cursos != 1 ? "s" : "" ?> asignado<?= $total_cursos != 1 ? "s" : "" ?>, con <?= $total_alumnos ?> alumno<?= $total_alumnos != 1 ? "s" : "" ?> en total.</p>
    </div>
</section>

<script src="../script.js"></script>
</body>
</html>