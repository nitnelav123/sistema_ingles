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

$id_horario  = $_POST["ID_horarios"] ?? "";
$id_curso    = $_POST["ID_curso"] ?? "";
$id_profesor = $_POST["ID_profesor"] ?? "";
$aula        = trim($_POST["aula_horarios"] ?? "");
$horaInicio  = $_POST["horaInicio_horarios"] ?? "";
$horaFin     = $_POST["horaFin_horarios"] ?? "";
$dia         = $_POST["DiaSemana_horarios"] ?? "";

$errores = [];

if ($id_horario === "")  { $errores[] = "Horario inválido."; }
if ($aula === "")        { $errores[] = "El aula es obligatoria."; }
if ($horaInicio === "")  { $errores[] = "La hora de inicio es obligatoria."; }
if ($horaFin === "")     { $errores[] = "La hora de fin es obligatoria."; }
if ($horaFin !== "" && $horaInicio !== "" && $horaFin <= $horaInicio) {
    $errores[] = "La hora de fin debe ser posterior a la de inicio.";
}

if (count($errores) > 0) {
    echo json_encode([
        "success" => false,
        "message" => implode(" ", $errores)
    ]);
    exit;
}

try {
    $sql = "UPDATE Horarios
            SET ID_profesor = ?, ID_curso = ?, aula_horarios = ?, horaInicio_horarios = ?, horaFin_horarios = ?, DiaSemana_horarios = ?
            WHERE ID_horarios = ?";
    $sentencia = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($sentencia, "iissssi", $id_profesor, $id_curso, $aula, $horaInicio, $horaFin, $dia, $id_horario);
    
    if (mysqli_stmt_execute($sentencia)) {
        echo json_encode([
            "success" => true,
            "message" => "Horario actualizado con éxito."
        ]);
        exit;
    } else {
        echo json_encode([
            "success" => false,
            "message" => "No se pudieron guardar los cambios en la base de datos."
        ]);
        exit;
    }
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error al actualizar el horario."
    ]);
    exit;
}
?>