<?php
session_start();
$conexion = require("../includes/conexion.php");

header('Content-Type: application/json');

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "admin") {
    echo json_encode(["success" => false, "message" => "Sesión no válida o caducada."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
    exit;
}

$dni       = $_POST["dni"] ?? "";
$nombre    = trim($_POST["nombre_persona"]    ?? "");
$apellido  = trim($_POST["apellido_persona"]  ?? "");
$email     = trim($_POST["email_persona"]     ?? "");
$telefono  = trim($_POST["telefono_persona"]  ?? "");
$fechaNac  = trim($_POST["fechaNac_persona"]  ?? "");

$errores = [];

if ($nombre === "")   { $errores[] = "El nombre es obligatorio."; }
if ($apellido === "") { $errores[] = "El apellido es obligatorio."; }
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errores[] = "El email no es válido."; }
if ($telefono === "") { $errores[] = "El teléfono es obligatorio."; }
if ($fechaNac === "") { $errores[] = "La fecha de nacimiento es obligatoria."; }

if (count($errores) > 0) {
    echo json_encode([
        "success" => false,
        "message" => implode(" ", $errores)
    ]);
    exit;
}

try {
    $sql = "UPDATE Persona
            SET nombre_persona = ?, apellido_persona = ?, email_persona = ?, telefono_persona = ?, fechaNac_persona = ?
            WHERE DNI_persona = ? AND rol_persona = 'alumno'";

    $sentencia = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($sentencia, "ssssss", $nombre, $apellido, $email, $telefono, $fechaNac, $dni);

    if (mysqli_stmt_execute($sentencia)) {
        echo json_encode([
            "success" => true,
            "message" => "Alumno actualizado correctamente."
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "No se pudieron guardar los cambios."
        ]);
    }
} catch (Exception $e) {
    $mensajeError = "Error al actualizar el alumno.";
    if (mysqli_errno($conexion) == 1062) {
        $mensajeError = "El correo electrónico ya está registrado por otra cuenta.";
    }

    echo json_encode([
        "success" => false,
        "message" => $mensajeError
    ]);
}
exit;
?>