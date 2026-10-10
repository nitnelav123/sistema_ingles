<?php
session_start();
$conexion = require("../includes/conexion.php");

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "admin") {
    header("Location: ../login.html");
    exit;
}

$errores = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_curso    = $_POST["ID_curso"] ?? "";
    $id_profesor = $_POST["ID_profesor"] ?? "";
    $aula        = trim($_POST["aula_horarios"] ?? "");
    $horaInicio  = $_POST["horaInicio_horarios"] ?? "";
    $horaFin     = $_POST["horaFin_horarios"] ?? "";
    $dia         = $_POST["DiaSemana_horarios"] ?? "";

    if ($aula === "")       { $errores[] = "El aula es obligatoria."; }
    if ($horaInicio === "") { $errores[] = "La hora de inicio es obligatoria."; }
    if ($horaFin === "")    { $errores[] = "La hora de fin es obligatoria."; }
    if ($horaFin !== "" && $horaInicio !== "" && $horaFin <= $horaInicio) {
        $errores[] = "La hora de fin debe ser posterior a la de inicio.";
    }

    if (count($errores) === 0) {
        $sql = "INSERT INTO Horarios (ID_profesor, ID_curso, aula_horarios, horaInicio_horarios, horaFin_horarios, DiaSemana_horarios)
                VALUES (?, ?, ?, ?, ?, ?)";
        $sentencia = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($sentencia, "iissss", $id_profesor, $id_curso, $aula, $horaInicio, $horaFin, $dia);
        mysqli_stmt_execute($sentencia);

        header("Location: admin-horarios.php");
        exit;
    }
}

$cursos = mysqli_query($conexion, "SELECT ID_curso, nombre_curso FROM Curso WHERE activo = TRUE ORDER BY nombre_curso");
$profesores = mysqli_query($conexion, "
    SELECT pr.ID_profesor, p.nombre_persona, p.apellido_persona
    FROM Profesores pr JOIN Persona p ON p.DNI_persona = pr.DNI_persona
    WHERE p.activo = TRUE ORDER BY p.apellido_persona");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo horario - New Ways</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link rel="stylesheet" href="../style.css?v=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<header class="header">
    <div class="header-container">
        <div class="header-left">
            <img src="img/Logo.png" class="logo">
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
        <h2>Nuevo horario</h2>

        <?php foreach ($errores as $error): ?>
            <p style="color: #dc2626;"><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>

        <form action="admin-alta-horario.php" method="POST">

            <select name="ID_curso" required>
                <option value="">-- Elegí un curso --</option>
                <?php while ($c = mysqli_fetch_assoc($cursos)): ?>
                    <option value="<?= $c['ID_curso'] ?>"><?= htmlspecialchars($c['nombre_curso']) ?></option>
                <?php endwhile; ?>
            </select>

            <select name="ID_profesor" required>
                <option value="">-- Elegí un profesor --</option>
                <?php while ($p = mysqli_fetch_assoc($profesores)): ?>
                    <option value="<?= $p['ID_profesor'] ?>"><?= htmlspecialchars($p['nombre_persona'] . " " . $p['apellido_persona']) ?></option>
                <?php endwhile; ?>
            </select>

            <select name="DiaSemana_horarios" required>
                <option value="">-- Día --</option>
                <option>Lunes</option>
                <option>Martes</option>
                <option>Miércoles</option>
                <option>Jueves</option>
                <option>Viernes</option>
                <option>Sábado</option>
            </select>

            <div class="form-row">
                <input type="time" id="horaInicio_horarios" name="horaInicio_horarios" required>
                <input type="time" id="horaFin_horarios" name="horaFin_horarios" required>
            </div>

            <input type="text" name="aula_horarios" placeholder="Aula" required>

            <button type="submit" class="hero-button">Crear horario</button>
        </form>
    </div>
</section>

<script src="../script.js"></script>
</body>
</html>