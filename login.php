<?php
session_start();

$conexion = require("includes/conexion.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Acceso no válido");
}

$identificador = trim($_POST["identificador"] ?? "");
$password      = $_POST["password"] ?? "";

if ($identificador === "" || $password === "") {
    echo "<p>Completá ambos campos.</p>";
    exit;
}

$sql = "SELECT DNI_persona, nombre_persona, password_persona, activo, rol_persona
        FROM Persona
        WHERE DNI_persona = ? OR email_persona = ?";

$sentencia = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($sentencia, "ss", $identificador, $identificador);
mysqli_stmt_execute($sentencia);

$resultado = mysqli_stmt_get_result($sentencia);
$persona   = mysqli_fetch_assoc($resultado);

if (!$persona || !password_verify($password, $persona["password_persona"])) {
    echo "<h2>❌ DNI/email o contraseña incorrectos</h2>";
    echo "<a href='login.html'>Volver a intentar</a>";
    exit;
}

if (!$persona["activo"]) {
    echo "<h2>⚠️ Tu cuenta está inactiva</h2>";
    echo "<p>Contactate con el instituto para reactivarla.</p>";
    exit;
}

$_SESSION["dni"]    = $persona["DNI_persona"];
$_SESSION["nombre"] = $persona["nombre_persona"];
$_SESSION["rol"]    = $persona["rol_persona"];

switch ($_SESSION["rol"]) {
    case 'admin':
        header("Location: ../panel-admin/panel-admin.php");
        break;
    case 'profesor':
        header("Location: ../panel-profesor/panel-profesor.php");
        break;
    default:
        header("Location: ../panel-alumno/panel-alumno.php");
        break;
}
exit;
?>