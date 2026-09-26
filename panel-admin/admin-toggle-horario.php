<?php
session_start();
$conexion = require("../includes/conexion.php");

header('Content-Type: application/json');

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "admin") {
    echo json_encode(["success" => false, "message" => "Sesión no válida o caducada."]);
    exit;
}

$id_horario = $_GET["id"] ?? "";

if ($id_horario === "") {
    echo json_encode(["success" => false, "message" => "ID de horario no válido."]);
    exit;
}

try {
    // Si querés eliminar el registro directamente:
    $sql = "DELETE FROM Horarios WHERE ID_horarios = ?";
    $sentencia = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($sentencia, "i", $id_horario);
    
    if (mysqli_stmt_execute($sentencia)) {
        echo json_encode(["success" => true, "message" => "Horario eliminado correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "No se pudo eliminar el horario."]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error al procesar la solicitud."]);
}
exit;
?>