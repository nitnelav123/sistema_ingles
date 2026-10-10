<?php
session_start();
$conexion = require("../includes/conexion.php");

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "admin") {
    header("Location: ../login.html");
    exit;
}

$id_horario = $_GET["id"] ?? "";

if ($id_horario === "") {
    header("Location: admin-horarios.php");
    exit;
}

$sentencia = mysqli_prepare($conexion, "SELECT * FROM Horarios WHERE ID_horarios = ?");
mysqli_stmt_bind_param($sentencia, "i", $id_horario);
mysqli_stmt_execute($sentencia);
$resultado = mysqli_stmt_get_result($sentencia);
$horario = mysqli_fetch_assoc($resultado);

if (!$horario) {
    header("Location: admin-horarios.php");
    exit;
}

$cursos = mysqli_query($conexion, "SELECT ID_curso, nombre_curso FROM Curso WHERE activo = TRUE ORDER BY nombre_curso");
$profesores = mysqli_query($conexion, "
    SELECT pr.ID_profesor, p.nombre_persona, p.apellido_persona
    FROM Profesores pr JOIN Persona p ON p.DNI_persona = pr.DNI_persona
    WHERE p.activo = TRUE ORDER BY p.apellido_persona");

$dias = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar horario - New Ways</title>
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
        <h2>Editar horario</h2>

        <form id="form-editar-horario" action="admin-actualizar-horario.php" method="POST">

            <input type="hidden" name="ID_horarios" value="<?= htmlspecialchars($horario['ID_horarios']) ?>">

            <select name="ID_curso" required>
                <option value="">-- Elegí un curso --</option>
                <?php while ($c = mysqli_fetch_assoc($cursos)): ?>
                    <option value="<?= $c['ID_curso'] ?>" <?= $c['ID_curso'] == $horario['ID_curso'] ? "selected" : "" ?>>
                        <?= htmlspecialchars($c['nombre_curso']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <select name="ID_profesor" required>
                <option value="">-- Elegí un profesor --</option>
                <?php while ($p = mysqli_fetch_assoc($profesores)): ?>
                    <option value="<?= $p['ID_profesor'] ?>" <?= $p['ID_profesor'] == $horario['ID_profesor'] ? "selected" : "" ?>>
                        <?= htmlspecialchars($p['nombre_persona'] . " " . $p['apellido_persona']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <select name="DiaSemana_horarios" required>
                <option value="">-- Día --</option>
                <?php foreach ($dias as $d): ?>
                    <option <?= $d === $horario['DiaSemana_horarios'] ? "selected" : "" ?>><?= $d ?></option>
                <?php endforeach; ?>
            </select>

            <div class="form-row">
                <input type="time" name="horaInicio_horarios" value="<?= htmlspecialchars(substr($horario['horaInicio_horarios'], 0, 5)) ?>" required>
                <input type="time" name="horaFin_horarios" value="<?= htmlspecialchars(substr($horario['horaFin_horarios'], 0, 5)) ?>" required>
            </div>

            <input type="text" name="aula_horarios" placeholder="Aula" value="<?= htmlspecialchars($horario['aula_horarios']) ?>" required>

            <button type="submit" class="hero-button">Guardar cambios</button>
        </form>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../script.js?v=1.1"></script>
<script>
    // Inicializamos el interceptor de envio de formulario
    manejarEnvioFormulario('form-editar-horario', 'admin-horarios.php', 'Horario actualizado correctamente');
</script>
</body>
</html>