<?php
session_start();

if (!isset($_SESSION["dni"])) {
    header("Location: login.html");
    exit;
}

$conexion = require("includes/conexion.php");
$dni = $_SESSION["dni"];

$nombre    = trim($_POST["nombre_persona"]    ?? "");
$apellido  = trim($_POST["apellido_persona"]  ?? "");
$email     = trim($_POST["email_persona"]     ?? "");
$telefono  = trim($_POST["telefono_persona"]  ?? "");
$fechaNac  = trim($_POST["fechaNac_persona"]  ?? "");

$errores = [];

if ($nombre === "")   { $errores[] = "El nombre es obligatorio."; }
if ($apellido === "") { $errores[] = "El apellido es obligatorio."; }
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "El email no es válido.";
}
if ($telefono === "") { $errores[] = "El teléfono es obligatorio."; }
if ($fechaNac === "") { $errores[] = "La fecha de nacimiento es obligatoria."; }

if (count($errores) > 0) {
    foreach ($errores as $error) {
        echo "<p>" . htmlspecialchars($error) . "</p>";
    }
    echo "<a href='perfil.php'>Volver</a>";
    exit;
}

$sql = "UPDATE Persona
        SET nombre_persona = ?, apellido_persona = ?, email_persona = ?, telefono_persona = ?, fechaNac_persona = ?
        WHERE DNI_persona = ?";

$sentencia = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($sentencia, "ssssss", $nombre, $apellido, $email, $telefono, $fechaNac, $dni);

if (mysqli_stmt_execute($sentencia)) {
    echo "<h2>✅ Datos actualizados correctamente</h2>";
    echo "<a href='perfil.php'>Volver a mi perfil</a>";
} else {
    if (mysqli_errno($conexion) == 1062) {
        echo "<h2>❌ Ese correo ya está en uso por otra cuenta</h2>";
    } else {
        echo "<h2>No se pudo actualizar</h2>";
    }
    echo "<a href='perfil.php'>Volver</a>";
}
?>