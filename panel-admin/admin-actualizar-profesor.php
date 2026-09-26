<?php
session_start();
$conexion = require("../includes/conexion.php");

// Declaramos que la respuesta SIEMPRE será un objeto JSON
header('Content-Type: application/json');

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "admin") {
    echo json_encode(["success" => false, "message" => "Sesión no válida o caducada."]);
    exit;
}

$dni            = $_POST["dni"] ?? "";
$nombre         = trim($_POST["nombre_persona"]    ?? "");
$apellido       = trim($_POST["apellido_persona"]  ?? "");
$email          = trim($_POST["email_persona"]     ?? "");
$telefono       = trim($_POST["telefono_persona"]  ?? "");
$fechaNac       = trim($_POST["fechaNac_persona"]  ?? "");
$disponibilidad = trim($_POST["disponibilidad"]    ?? "");
$salario        = trim($_POST["salario_profesor"]  ?? "");
$cargaHoraria   = trim($_POST["cargaHoraria"]       ?? "");

// Validaciones
$errores = [];
if ($nombre === "")   { $errores[] = "El nombre es obligatorio."; }
if ($apellido === "") { $errores[] = "El apellido es obligatorio."; }
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errores[] = "El email no es válido."; }
if ($telefono === "") { $errores[] = "El teléfono es obligatorio."; }
if ($fechaNac === "") { $errores[] = "La fecha de nacimiento es obligatoria."; }

// Si hay errores de validación, los enviamos juntos al cliente
if (count($errores) > 0) {
    echo json_encode([
        "success" => false, 
        "message" => implode(" ", $errores)
    ]);
    exit;
}

$salario_val      = $salario !== "" ? $salario : null;
$cargaHoraria_val = $cargaHoraria !== "" ? $cargaHoraria : null;

mysqli_begin_transaction($conexion);

try {
    // 1. Actualizar datos en Persona
    $sql = "UPDATE Persona
            SET nombre_persona = ?, apellido_persona = ?, email_persona = ?, telefono_persona = ?, fechaNac_persona = ?
            WHERE DNI_persona = ? AND rol_persona = 'profesor'";
    $sentencia = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($sentencia, "ssssss", $nombre, $apellido, $email, $telefono, $fechaNac, $dni);
    mysqli_stmt_execute($sentencia);

    // 2. Actualizar datos en Profesores
    $sql_prof = "UPDATE Profesores
                 SET disponibilidad = ?, salario_profesor = ?, cargaHoraria = ?
                 WHERE DNI_persona = ?";
    $sentencia_prof = mysqli_prepare($conexion, $sql_prof);
    mysqli_stmt_bind_param($sentencia_prof, "siis", $disponibilidad, $salario_val, $cargaHoraria_val, $dni);
    mysqli_stmt_execute($sentencia_prof);

    mysqli_commit($conexion);

    // Respuesta de éxito para SweetAlert2
    echo json_encode([
        "success" => true, 
        "message" => "Profesor actualizado correctamente."
    ]);
    exit;

} catch (Exception $e) {
    mysqli_rollback($conexion);
    
    $mensajeError = "No se pudieron guardar los cambios.";
    if (mysqli_errno($conexion) == 1062) {
        $mensajeError = "El correo electrónico ya está registrado por otra cuenta.";
    }

    echo json_encode([
        "success" => false, 
        "message" => $mensajeError
    ]);
    exit;
}
?>