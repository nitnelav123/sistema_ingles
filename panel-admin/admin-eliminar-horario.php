<?php
session_start();
$conexion = require("../includes/conexion.php");

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "admin") {
    header("Location: ../login.html");
    exit;
}

$id = $_GET["id"] ?? "";

$sql = "DELETE FROM Horarios WHERE ID_horarios = ?";
$sentencia = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($sentencia, "i", $id);

if (mysqli_stmt_execute($sentencia)) {
    header("Location: admin-horarios.php");
    exit;
} else {
    echo "<h2>❌ No se puede eliminar: este horario tiene alumnos inscriptos.</h2>";
    echo "<p>Primero hay que dar de baja esas inscripciones si realmente querés eliminarlo.</p>";
    echo "<a href='admin-horarios.php'>Volver</a>";
}
?>