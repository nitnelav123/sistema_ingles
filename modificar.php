<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["dni"])) {
    echo json_encode(["success" => false, "message" => "Tu sesión expiró. Volvé a iniciar sesión."]);
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
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errores[] = "El email no es válido."; }
if ($telefono === "") { $errores[] = "El teléfono es obligatorio."; }
if ($fechaNac === "") { $errores[] = "La fecha de nacimiento es obligatoria."; }

if (count($errores) > 0) {
    echo json_encode(["success" => false, "message" => implode(" ", $errores)]);
    exit;
}

$sql = "UPDATE Persona
        SET nombre_persona = ?, apellido_persona = ?, email_persona = ?, telefono_persona = ?, fechaNac_persona = ?
        WHERE DNI_persona = ?";

try {
    $sentencia = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($sentencia, "ssssss", $nombre, $apellido, $email, $telefono, $fechaNac, $dni);
    mysqli_stmt_execute($sentencia);

    $_SESSION["nombre"] = $nombre;
    echo json_encode(["success" => true, "message" => "Datos actualizados correctamente"]);
} catch (Exception $e) {
    if ($e->getCode() == 1062) {
        echo json_encode(["success" => false, "message" => "Ese correo ya está en uso por otra cuenta."]);
    } else {
        echo json_encode(["success" => false, "message" => "No se pudo actualizar los datos."]);
    }
}
