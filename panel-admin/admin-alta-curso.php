<?php
session_start();
$conexion = require("../includes/conexion.php");

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "admin") {
    header("Location: ../login.html");
    exit;
}

$errores = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre_curso"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");

    if ($nombre === "") { $errores[] = "El nombre del curso es obligatorio."; }

    if (count($errores) === 0) {
        $sql = "INSERT INTO Curso (nombre_curso, descripcion) VALUES (?, ?)";
        $sentencia = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($sentencia, "ss", $nombre, $descripcion);
        mysqli_stmt_execute($sentencia);

        header("Location: admin-cursos.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo curso - New Ways</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link rel="stylesheet" href="../style.css?v=1.0">
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
        <h2>Nuevo curso</h2>

        <?php foreach ($errores as $error): ?>
            <p style="color: #dc2626;"><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>

        <form action="admin-alta-curso.php" method="POST">
            <input type="text" name="nombre_curso" placeholder="Nombre del curso (ej: Inglés Inicial)" required>
            <input type="text" name="descripcion" placeholder="Descripción (opcional)">
            <button type="submit" class="hero-button">Crear curso</button>
        </form>
    </div>
</section>

<script src="../script.js"></script>
</body>
</html>