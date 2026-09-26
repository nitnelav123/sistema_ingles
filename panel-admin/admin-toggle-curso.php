<?php
session_start();
$conexion = require("../includes/conexion.php");

header('Content-Type: application/json');

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "admin") {
    echo json_encode(["success" => false, "message" => "Sesión no válida o caducada."]);
    exit;
}

$id = $_GET["id"] ?? "";

if ($id === "") {
    echo json_encode(["success" => false, "message" => "ID de curso no válido."]);
    exit;
}

try {
    $sql = "UPDATE Curso SET activo = NOT activo WHERE ID_curso = ?";
    $sentencia = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($sentencia, "i", $id);
    
    if (mysqli_stmt_execute($sentencia)) {
        echo json_encode(["success" => true, "message" => "Estado del curso actualizado."]);
    } else {
        echo json_encode(["success" => false, "message" => "No se pudo cambiar el estado del curso."]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error al procesar la solicitud."]);
}
exit;
?>