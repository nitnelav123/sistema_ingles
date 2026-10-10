<?php
session_start();
$conexion = require("../includes/conexion.php");

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "admin") {
    header("Location: ../login.html");
    exit;
}

$sql = "SELECT h.ID_horarios, h.aula_horarios, h.horaInicio_horarios, h.horaFin_horarios, h.DiaSemana_horarios,
               c.nombre_curso, p.nombre_persona, p.apellido_persona
        FROM Horarios h
        JOIN Curso c ON c.ID_curso = h.ID_curso
        JOIN Profesores pr ON pr.ID_profesor = h.ID_profesor
        JOIN Persona p ON p.DNI_persona = pr.DNI_persona
        ORDER BY c.nombre_curso, h.DiaSemana_horarios";
$resultado = mysqli_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios - New Ways</title>
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
    <div class="form-card admin-table-card">
        <h2>Horarios</h2>
        <a href="admin-alta-horario.php" class="hero-button" style="display:inline-block; text-decoration:none;">+ Agregar horario</a>

        <div class="admin-table-wrapper">
        <table class="admin-table">
            <tr>
                <th>Curso</th>
                <th>Profesor</th>
                <th>Día</th>
                <th>Horario</th>
                <th>Aula</th>
                <th>Acciones</th>
            </tr>
            <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <td><?= htmlspecialchars($fila["nombre_curso"]) ?></td>
                <td><?= htmlspecialchars($fila["nombre_persona"] . " " . $fila["apellido_persona"]) ?></td>
                <td><?= htmlspecialchars($fila["DiaSemana_horarios"]) ?></td>
                <td><?= htmlspecialchars(substr($fila["horaInicio_horarios"], 0, 5) . " - " . substr($fila["horaFin_horarios"], 0, 5)) ?></td>
                <td><?= htmlspecialchars($fila["aula_horarios"]) ?></td>
                <td>
                    <a href="admin-editar-horario.php?id=<?= $fila["ID_horarios"] ?>">Editar</a>
                    |
                    <button type="button" class="btn-link-eliminar" 
                            style=""
                            onclick="confirmarEliminacionGenerica('<?= $fila['ID_horarios'] ?>', '<?= htmlspecialchars($fila['nombre_curso'] . ' - ' . $fila['DiaSemana_horarios']) ?>', 'admin-toggle-horario.php', 'horario')">
                        Eliminar
                    </button>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../script.js?v=1.1"></script>
</body>
</html>