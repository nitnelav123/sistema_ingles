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

$sql = "SELECT p.nombre_persona, p.apellido_persona, p.email_persona, c.nombre_curso,
               h.DiaSemana_horarios, h.horaInicio_horarios, h.horaFin_horarios
        FROM Inscripcion i
        JOIN Horarios h ON h.ID_horarios = i.ID_horarios
        JOIN Curso c ON c.ID_curso = h.ID_curso
        JOIN Cliente cl ON cl.ID_cliente = i.ID_cliente
        JOIN Persona p ON p.DNI_persona = cl.DNI_persona
        WHERE h.ID_profesor = ? AND i.estado = 'activa'
        ORDER BY c.nombre_curso, p.apellido_persona";
$sentencia = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($sentencia, "i", $id_profesor);
mysqli_stmt_execute($sentencia);
$resultado = mysqli_stmt_get_result($sentencia);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis alumnos - New Ways</title>
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
    <div class="form-card admin-table-card">
        <h2>Mis alumnos</h2>

        <?php if (mysqli_num_rows($resultado) === 0): ?>
            <p>Todavía no tenés alumnos inscriptos en tus cursos.</p>
        <?php else: ?>
        <table class="admin-table">
            <tr>
                <th>Alumno</th>
                <th>Email</th>
                <th>Curso</th>
                <th>Día / Horario</th>
            </tr>
            <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <td><?= htmlspecialchars($fila["nombre_persona"] . " " . $fila["apellido_persona"]) ?></td>
                <td><?= htmlspecialchars($fila["email_persona"]) ?></td>
                <td><?= htmlspecialchars($fila["nombre_curso"]) ?></td>
                <td><?= htmlspecialchars($fila["DiaSemana_horarios"] . " " . $fila["horaInicio_horarios"] . "-" . $fila["horaFin_horarios"]) ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php endif; ?>
    </div>
</section>

<script src="../script.js"></script>
</body>
</html>