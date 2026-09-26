<?php
session_start();
if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "alumno") {
    header("Location: ../login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi curso - New Ways</title>
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
        <p>Todavía no tenés un curso asignado.</p>
    </div>
</section>

<script src="../script.js"></script>
</body>
</html>