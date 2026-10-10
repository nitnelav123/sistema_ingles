<?php
session_start();
$conexion = require("../includes/conexion.php");

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "alumno") {
    header("Location: ../login.html");
    exit;
}

$sql = "SELECT c.nombre_curso, h.DiaSemana_horarios, h.horaInicio_horarios, h.horaFin_horarios, h.aula_horarios,
               p.nombre_persona AS nombre_profesor, p.apellido_persona AS apellido_profesor
        FROM Inscripcion i
        JOIN Cliente cl ON cl.ID_cliente = i.ID_cliente
        JOIN Horarios h ON h.ID_horarios = i.ID_horarios
        JOIN Curso c ON c.ID_curso = h.ID_curso
        JOIN Profesores pr ON pr.ID_profesor = h.ID_profesor
        JOIN Persona p ON p.DNI_persona = pr.DNI_persona
        WHERE cl.DNI_persona = ? AND i.estado = 'activa'";
$sentencia = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($sentencia, "s", $_SESSION["dni"]);
mysqli_stmt_execute($sentencia);
$resultado = mysqli_stmt_get_result($sentencia);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi curso - New Ways</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../style.css?v=1.1">
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
        <h2>Mi curso</h2>

        <?php if (mysqli_num_rows($resultado) === 0): ?>
            <p>Todavía no tenés un curso asignado.</p>
        <?php else: ?>
            <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                <p><strong>Curso:</strong> <?= htmlspecialchars($fila["nombre_curso"]) ?></p>
                <p><strong>Profesor:</strong> <?= htmlspecialchars($fila["nombre_profesor"] . " " . $fila["apellido_profesor"]) ?></p>
                <p><strong>Día y horario:</strong> <?= htmlspecialchars($fila["DiaSemana_horarios"] . " " . $fila["horaInicio_horarios"] . " - " . $fila["horaFin_horarios"]) ?></p>
                <p><strong>Aula:</strong> <?= htmlspecialchars($fila["aula_horarios"]) ?></p>
                <hr style="margin: 20px 0; border-color: var(--borde);">
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</section>

<script src="../script.js"></script>
</body>
</html>