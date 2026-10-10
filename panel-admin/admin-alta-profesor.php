<?php
session_start();
$conexion = require("../includes/conexion.php");

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "admin") {
    header("Location: ../login.html");
    exit;
}

$errores = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre         = trim($_POST["nombre_persona"]    ?? "");
    $apellido       = trim($_POST["apellido_persona"]  ?? "");
    $dni            = trim($_POST["DNI_persona"]       ?? "");
    $email          = trim($_POST["email_persona"]     ?? "");
    $telefono       = trim($_POST["telefono_persona"]  ?? "");
    $fechaNac       = trim($_POST["fechaNac_persona"]  ?? "");
    $password       = $_POST["password_persona"]        ?? "";
    $disponibilidad = trim($_POST["disponibilidad"]    ?? "");
    $salario        = trim($_POST["salario_profesor"]  ?? "");
    $cargaHoraria   = trim($_POST["cargaHoraria"]       ?? "");

    if ($nombre === "")   { $errores[] = "El nombre es obligatorio."; }
    if ($apellido === "") { $errores[] = "El apellido es obligatorio."; }
    if (!ctype_digit($dni) || strlen($dni) < 7 || strlen($dni) > 8) {
        $errores[] = "El DNI debe tener entre 7 y 8 dígitos.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errores[] = "El email no es válido."; }
    if (strlen($password) < 6) { $errores[] = "La contraseña inicial debe tener al menos 6 caracteres."; }
    if ($telefono === "") { $errores[] = "El teléfono es obligatorio."; }
    if ($fechaNac === "") { $errores[] = "La fecha de nacimiento es obligatoria."; }

    if (count($errores) === 0) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $salario_val      = $salario !== "" ? $salario : null;
        $cargaHoraria_val = $cargaHoraria !== "" ? $cargaHoraria : null;

        mysqli_begin_transaction($conexion);
        try {
            $sql = "INSERT INTO Persona (DNI_persona, nombre_persona, apellido_persona, email_persona, telefono_persona, fechaNac_persona, password_persona, rol_persona)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'profesor')";
            $sentencia = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($sentencia, "sssssss", $dni, $nombre, $apellido, $email, $telefono, $fechaNac, $password_hash);
            mysqli_stmt_execute($sentencia);

            $sql_prof = "INSERT INTO Profesores (DNI_persona, disponibilidad, salario_profesor, cargaHoraria)
                         VALUES (?, ?, ?, ?)";
            $sentencia_prof = mysqli_prepare($conexion, $sql_prof);
            mysqli_stmt_bind_param($sentencia_prof, "ssii", $dni, $disponibilidad, $salario_val, $cargaHoraria_val);
            mysqli_stmt_execute($sentencia_prof);

            mysqli_commit($conexion);

            header("Location: admin-profesores.php");
            exit;

        } catch (Exception $e) {
            mysqli_rollback($conexion);
            if (mysqli_errno($conexion) == 1062) {
                $errores[] = str_contains(mysqli_error($conexion), "PRIMARY")
                    ? "Ya existe un usuario registrado con ese DNI."
                    : "Ese correo ya ha sido registrado.";
            } else {
                $errores[] = "No se pudo crear el profesor.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo profesor - New Ways</title>
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
        <h2>Nuevo profesor</h2>

        <?php foreach ($errores as $error): ?>
            <p style="color: #dc2626;"><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>

        <form action="admin-alta-profesor.php" method="POST">

            <div class="form-row">
                <input type="text" name="nombre_persona" placeholder="Nombre" required>
                <input type="text" name="apellido_persona" placeholder="Apellido" required>
            </div>

            <div class="form-row">
                <input type="text" inputmode="numeric" pattern="[0-9]{7,8}" name="DNI_persona" placeholder="DNI" required>
                <input type="tel" name="telefono_persona" placeholder="Teléfono" required>
            </div>

            <input type="email" name="email_persona" placeholder="Email" required>

            <input type="password" name="password_persona" placeholder="Contraseña inicial" required minlength="6">

            <label class="form-date-label">
                Fecha de nacimiento
                <input type="date" name="fechaNac_persona" min="1920-01-01" max="2020-12-31" required>
            </label>

            <input type="text" name="disponibilidad" placeholder="Disponibilidad (ej: Lunes a Viernes tarde)">

            <div class="form-row">
                <input type="number" name="salario_profesor" placeholder="Salario">
                <input type="number" name="cargaHoraria" placeholder="Carga horaria (hs)">
            </div>

            <button type="submit" class="hero-button">Crear profesor</button>
        </form>
    </div>
</section>

<script src="../script.js"></script>
</body>
</html>