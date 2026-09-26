<?php
session_start();
$conexion = require("../includes/conexion.php");

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "admin") {
    header("Location: ../login.html");
    exit;
}

$resultado = mysqli_query($conexion, "SELECT * FROM Curso ORDER BY nombre_curso");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos - New Ways</title>
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
    <div class="form-card admin-table-card">
        <h2>Cursos</h2>
        <a href="admin-alta-curso.php" class="hero-button" style="display:inline-block; text-decoration:none;">+ Agregar curso</a>

        <div class="admin-table-wrapper">
        <table class="admin-table">
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
            <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <td><?= htmlspecialchars($fila["nombre_curso"]) ?></td>
                <td><?= htmlspecialchars($fila["descripcion"] ?? "-") ?></td>
                <td><?= $fila["activo"] ? "Activo" : "Inactivo" ?></td>
                <td>
                    <a href="admin-editar-curso.php?id=<?= $fila["ID_curso"] ?>">Editar</a>
                    |
                    <button type="button" class="btn-accion" 
                        style="background:none; border:none; color:inherit; cursor:pointer; font-size:inherit; padding:0"
                        onclick="confirmarToggleGenerico('<?= $fila['ID_curso'] ?>', '<?= htmlspecialchars($fila['nombre_curso'], ENT_QUOTES) ?>', <?= $fila['activo'] ? 'true' : 'false' ?>, 'admin-toggle-curso.php', 'curso')">
                        <?= $fila['activo'] ? "Desactivar" : "Activar" ?>
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