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

$id = $_POST["id"] ?? "";
$nombre = trim($_POST["nombre_curso"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");

if ($id === "") {
    echo json_encode(["success" => false, "message" => "ID de curso no válido."]);
    exit;
}

if ($nombre === "") {
    echo json_encode(["success" => false, "message" => "El nombre del curso es obligatorio."]);
    exit;
}

try {
    $sql = "UPDATE Curso SET nombre_curso = ?, descripcion = ? WHERE ID_curso = ?";
    $sentencia = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($sentencia, "ssi", $nombre, $descripcion, $id);
    
    if (mysqli_stmt_execute($sentencia)) {
        echo json_encode([
            "success" => true,
            "message" => "Curso actualizado correctamente."
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "No se pudieron guardar los cambios."
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error al actualizar el curso."
    ]);
}
exit;
?>