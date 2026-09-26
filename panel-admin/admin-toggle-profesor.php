<?php
session_start();
$conexion = require("../includes/conexion.php");

header('Content-Type: application/json');

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "admin") {
    echo json_encode(["success" => false, "message" => "No tienes permisos o la sesión caducó."]);
    exit;
}

$dni = $_GET["id"] ?? $_GET["dni"] ?? "";

if (!empty($dni)) {
    try {
        // Actualizamos el estado directamente por DNI
        $sql = "UPDATE Persona SET activo = NOT activo WHERE DNI_persona = ?";
        $sentencia = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($sentencia, "s", $dni);

        if (mysqli_stmt_execute($sentencia)) {
            // Verificamos si realmente se modificó alguna fila
            if (mysqli_stmt_affected_rows($sentencia) > 0) {
                echo json_encode(["success" => true, "message" => "Estado actualizado correctamente."]);
                exit;
            } else {
                echo json_encode(["success" => false, "message" => "No se encontró ninguna persona con el DNI proporcionado ($dni)."]);
                exit;
            }
        } else {
            echo json_encode(["success" => false, "message" => "Error al ejecutar la consulta MySQL."]);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error de servidor: " . $e->getMessage()]);
        exit;
    }
}

echo json_encode(["success" => false, "message" => "DNI no proporcionado."]);
exit;