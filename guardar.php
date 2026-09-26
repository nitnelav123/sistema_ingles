<?php

$conexion = require("includes/conexion.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Acceso no válido");
}

$nombre             = trim($_POST["nombre_persona"]    ?? "");
$apellido           = trim($_POST["apellido_persona"]  ?? "");
$dni                = trim($_POST["DNI_persona"]       ?? "");
$email              = trim($_POST["email_persona"]     ?? "");
$telefono           = trim($_POST["telefono_persona"]  ?? "");
$fechaNac           = trim($_POST["fechaNac_persona"]  ?? "");
$password           = $_POST["password_persona"]       ?? "";
$password_confirmar = $_POST["password_confirmar"]     ?? "";

$errores = [];

if ($nombre === "")   { $errores[] = "El nombre es obligatorio."; }
if ($apellido === "") { $errores[] = "El apellido es obligatorio."; }

if (!ctype_digit($dni) || strlen($dni) < 7 || strlen($dni) > 8) {
    $errores[] = "El DNI debe tener entre 7 y 8 dígitos.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "El email no es válido.";
}

if (strlen($password) < 6) {
    $errores[] = "La contraseña debe tener al menos 6 caracteres.";
}

if ($password !== $password_confirmar) {
    $errores[] = "Las contraseñas no coinciden.";
}

if ($telefono === "") { $errores[] = "El teléfono es obligatorio."; }

if ($fechaNac === "") {
    $errores[] = "La fecha de nacimiento es obligatoria.";
} else {
    $fecha_obj = DateTime::createFromFormat('Y-m-d', $fechaNac);
    $hoy = new DateTime();

    if (!$fecha_obj || $fecha_obj > $hoy || $hoy->diff($fecha_obj)->y > 110) {
        $errores[] = "La fecha de nacimiento no es válida.";
    }
}

if (count($errores) > 0) {
    foreach ($errores as $error) {
        echo "<p>" . htmlspecialchars($error) . "</p>";
    }
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);


mysqli_begin_transaction($conexion);

try {
    $sql = "INSERT INTO Persona (DNI_persona, nombre_persona, apellido_persona, email_persona, telefono_persona, fechaNac_persona, password_persona, rol_persona)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'alumno')";

    $sentencia = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($sentencia, "sssssss", $dni, $nombre, $apellido, $email, $telefono, $fechaNac, $password_hash);
    mysqli_stmt_execute($sentencia);

    $sql_cliente = "INSERT INTO Cliente (DNI_persona) VALUES (?)";
    $sentencia_cliente = mysqli_prepare($conexion, $sql_cliente);
    mysqli_stmt_bind_param($sentencia_cliente, "s", $dni);
    mysqli_stmt_execute($sentencia_cliente);

    mysqli_commit($conexion);

    echo "<h2>✅ Se ha registrado correctamente</h2>";
    echo "<a href='login.html'>Volver al inicio</a>";

} catch (Exception $e) {
    mysqli_rollback($conexion);
    $error_codigo = mysqli_errno($conexion);

    if ($error_codigo == 1062) {
        if (str_contains(mysqli_error($conexion), "PRIMARY")) {
            echo "<h2>❌ Ya existe un usuario registrado con ese DNI</h2>";
        } else {
            echo "<h2>❌ Ese correo ya ha sido registrado</h2>";
        }
    } else {
        echo "<h2>❌ No se pudo guardar el registro</h2>";
    }
}
?>