<?php
session_start();

if (!isset($_SESSION["dni"])) {
    header("Location: login.html");
    exit;
}

$conexion = require("includes/conexion.php");
$dni = $_SESSION["dni"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    header("Content-Type: application/json");

    $password_actual    = $_POST["password_actual"]          ?? "";
    $password_nueva     = $_POST["password_nueva"]            ?? "";
    $password_confirmar = $_POST["password_nueva_confirmar"]  ?? "";

    $sql = "SELECT password_persona FROM Persona WHERE DNI_persona = ?";
    $sentencia = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($sentencia, "s", $dni);
    mysqli_stmt_execute($sentencia);
    $fila = mysqli_stmt_get_result($sentencia)->fetch_assoc();

    $errores = [];

    if (!password_verify($password_actual, $fila["password_persona"])) {
        $errores[] = "La contraseña actual es incorrecta.";
    }
    if (strlen($password_nueva) < 6) {
        $errores[] = "La nueva contraseña debe tener al menos 6 caracteres.";
    }
    if ($password_nueva !== $password_confirmar) {
        $errores[] = "Las contraseñas nuevas no coinciden.";
    }
    if ($password_actual !== "" && $password_nueva === $password_actual) {
        $errores[] = "La nueva contraseña no puede ser igual a la actual.";
    }

    if (count($errores) > 0) {
        echo json_encode(["success" => false, "message" => implode(" ", $errores)]);
        exit;
    }

    try {
        $nuevo_hash = password_hash($password_nueva, PASSWORD_DEFAULT);

        $sql_update = "UPDATE Persona SET password_persona = ? WHERE DNI_persona = ?";
        $sentencia_update = mysqli_prepare($conexion, $sql_update);
        mysqli_stmt_bind_param($sentencia_update, "ss", $nuevo_hash, $dni);
        mysqli_stmt_execute($sentencia_update);

        echo json_encode(["success" => true, "message" => "Contraseña actualizada correctamente"]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "No se pudo actualizar la contraseña."]);
    }
    exit;
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar contraseña - New Ways</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="style.css?v=1.1">
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
            <a href="panel-alumno/panel-alumno.php">Inicio</a>
            <a href="panel-alumno/mi-curso.php">Mi curso</a>
            <a href="panel-alumno/perfil.php">Perfil</a>
            <a href="logout.php">Cerrar sesión</a>
        </nav>

        <div class="toggle-container">
            <input type="checkbox" id="modoToggle">
            <label for="modoToggle" class="toggle"></label>
        </div>
    </div>
</header>

<section class="form-section">
    <div class="form-card">
        <h2>Cambiar contraseña</h2>

        <form id="form-password" action="cambiar-password.php" method="POST"
              data-ajax
              data-pregunta="¿Actualizar la contraseña?"
              data-exito="Contraseña actualizada correctamente"
              data-redireccion="panel-alumno/perfil.php">

            <input type="password" name="password_actual" placeholder="Contraseña actual" required>

            <div class="form-row">
                <input type="password" name="password_nueva" placeholder="Nueva contraseña" required minlength="6">
                <input type="password" name="password_nueva_confirmar" placeholder="Repetir nueva contraseña" required minlength="6">
            </div>

            <button type="submit" class="hero-button">Actualizar contraseña</button>

        </form>
    </div>
</section>

<script src="script.js?v=1.2"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>