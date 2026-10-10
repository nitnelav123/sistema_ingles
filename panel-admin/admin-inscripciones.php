<?php
session_start();
$conexion = require("../includes/conexion.php");

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "admin") {
    header("Location: ../login.html");
    exit;
}

$sql = "SELECT i.ID_inscripcion, i.estado, i.fechaAlta_inscripcion,
               p.nombre_persona, p.apellido_persona,
               c.nombre_curso, h.DiaSemana_horarios, h.horaInicio_horarios, h.horaFin_horarios
        FROM Inscripcion i
        JOIN Cliente cl ON cl.ID_cliente = i.ID_cliente
        JOIN Persona p ON p.DNI_persona = cl.DNI_persona
        JOIN Horarios h ON h.ID_horarios = i.ID_horarios
        JOIN Curso c ON c.ID_curso = h.ID_curso
        ORDER BY i.estado, c.nombre_curso, p.apellido_persona";
$resultado = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscripciones - New Ways</title>
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
        <h2>Inscripciones</h2>
        <a href="admin-alta-inscripcion.php" class="hero-button" style="display:inline-block; text-decoration:none; margin-bottom:20px;">+ Inscribir alumno</a>

        <table class="admin-table">
            <tr>
                <th>Alumno</th>
                <th>Curso</th>
                <th>Día / Horario</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
            <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <td><?= htmlspecialchars($fila["nombre_persona"] . " " . $fila["apellido_persona"]) ?></td>
                <td><?= htmlspecialchars($fila["nombre_curso"]) ?></td>
                <td><?= htmlspecialchars($fila["DiaSemana_horarios"] . " " . $fila["horaInicio_horarios"] . "-" . $fila["horaFin_horarios"]) ?></td>
                <td><?= $fila["estado"] === "activa" ? "Activa" : "Cancelada" ?></td>
               <td>
                    <?php if ($fila["estado"] === "activa"): ?>
                        <button type="button" class="btn-link-eliminar btn-inscripcion"
                            data-accion="cancelar"
                            data-id="<?= $fila["ID_inscripcion"] ?>"
                            data-detalle="<?= htmlspecialchars($fila["nombre_persona"] . " " . $fila["apellido_persona"] . " — " . $fila["nombre_curso"]) ?>">
                            Cancelar
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn-accion btn-inscripcion"
                            data-accion="reactivar"
                            data-id="<?= $fila["ID_inscripcion"] ?>"
                            data-detalle="<?= htmlspecialchars($fila["nombre_persona"] . " " . $fila["apellido_persona"] . " — " . $fila["nombre_curso"]) ?>">
                            Reactivar
                        </button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../script.js"></script>
</body>
</html>