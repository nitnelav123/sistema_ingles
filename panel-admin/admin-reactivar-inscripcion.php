<?php
session_start();
$conexion = require("../includes/conexion.php");
header("Content-Type: application/json");

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "admin") {
    echo json_encode(["success" => false, "message" => "No tenés permiso para realizar esta acción."]);
    exit;
}

$id = $_GET["id"] ?? "";

$sql = "SELECT i.estado, h.cupo_maximo,
               (SELECT COUNT(*) FROM Inscripcion WHERE ID_horarios = i.ID_horarios AND estado = 'activa') AS ocupados
        FROM Inscripcion i
        JOIN Horarios h ON h.ID_horarios = i.ID_horarios
        WHERE i.ID_inscripcion = ?";
$sentencia = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($sentencia, "i", $id);
mysqli_stmt_execute($sentencia);
$info = mysqli_stmt_get_result($sentencia)->fetch_assoc();

if (!$info || $info["estado"] !== "cancelada") {
    echo json_encode(["success" => false, "message" => "La inscripción no existe o ya está activa."]);
    exit;
}

if ($info["ocupados"] >= $info["cupo_maximo"]) {
    echo json_encode(["success" => false, "message" => "Ese horario ya alcanzó el cupo máximo de alumnos."]);
    exit;
}

$sql_update = "UPDATE Inscripcion SET estado = 'activa' WHERE ID_inscripcion = ?";
$sentencia_update = mysqli_prepare($conexion, $sql_update);
mysqli_stmt_bind_param($sentencia_update, "i", $id);
mysqli_stmt_execute($sentencia_update);

echo json_encode(["success" => true, "message" => "La inscripción fue reactivada."]);