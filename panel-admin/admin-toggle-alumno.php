<?php
session_start();
$conexion = require("../includes/conexion.php");

header('Content-Type: application/json');

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "admin") {
    echo json_encode(["success" => false, "message" => "Sesión no válida o caducada."]);
    exit;
}

$dni = $_GET["id"] ?? $_GET["dni"] ?? "";

if (!empty($dni)) {
    try {
        $sql = "UPDATE Persona SET activo = NOT activo WHERE DNI_persona = ? AND rol_persona = 'alumno'";
        $sentencia = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($sentencia, "s", $dni);

        if (mysqli_stmt_execute($sentencia)) {
            if (mysqli_stmt_affected_rows($sentencia) > 0) {
                echo json_encode(["success" => true, "message" => "Estado del alumno actualizado correctamente."]);
                exit;
            } else {
                echo json_encode(["success" => false, "message" => "No se encontró ningún alumno con el DNI proporcionado."]);
                exit;
            }
        } else {
            echo json_encode(["success" => false, "message" => "Error al ejecutar la consulta."]);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error de servidor: " . $e->getMessage()]);
        exit;
    }
}

echo json_encode(["success" => false, "message" => "DNI no proporcionado."]);
exit;
?>