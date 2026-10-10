<?php
session_start();
$conexion = require("../includes/conexion.php");
header("Content-Type: application/json");

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "admin") {
    echo json_encode(["success" => false, "message" => "No tenés permiso para realizar esta acción."]);
    exit;
}

$id = $_GET["id"] ?? "";

$sql = "UPDATE Inscripcion SET estado = 'cancelada' WHERE ID_inscripcion = ? AND estado = 'activa'";
$sentencia = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($sentencia, "i", $id);
mysqli_stmt_execute($sentencia);

if (mysqli_stmt_affected_rows($sentencia) === 1) {
    echo json_encode(["success" => true, "message" => "La inscripción fue cancelada."]);
} else {
    echo json_encode(["success" => false, "message" => "No se pudo cancelar la inscripción."]);
}