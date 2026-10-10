<?php
session_start();

// Indicamos que la respuesta será en formato JSON
header('Content-Type: application/json; charset=utf-8');

$conexion = require("includes/conexion.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        'success' => false,
        'title'   => 'Error',
        'message' => 'Acceso no válido.',
        'icon'    => 'error'
    ]);
    exit;
}

$identificador = trim($_POST["identificador"] ?? "");
$password      = $_POST["password"] ?? "";

if ($identificador === "" || $password === "") {
    echo json_encode([
        'success' => false,
        'title'   => 'Campos incompletos',
        'message' => 'Por favor, completá ambos campos.',
        'icon'    => 'warning'
    ]);
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

// 1. Verificación de credenciales (DNI/email o contraseña)
if (!$persona || !password_verify($password, $persona["password_persona"])) {
    echo json_encode([
        'success' => false,
        'title'   => 'Datos incorrectos',
        'message' => 'Contraseña o Email/DNI incorrectos.',
        'icon'    => 'error'
    ]);
    exit;
}

// 2. Verificación de estado activo/inactivo
if (!$persona["activo"]) {
    echo json_encode([
        'success' => false,
        'title'   => 'Cuenta inactiva',
        'message' => 'Tu cuenta se encuentra inactiva. Contactate con el instituto para reactivarla.',
        'icon'    => 'warning'
    ]);
    exit;
}

// 3. Inicio de sesión y redirección según rol
$_SESSION["dni"]    = $persona["DNI_persona"];
$_SESSION["nombre"] = $persona["nombre_persona"];
$_SESSION["rol"]    = $persona["rol_persona"];

$redirectUrl = '';

switch ($_SESSION["rol"]) {
    case 'admin':
        $redirectUrl = 'panel-admin/panel-admin.php';
        break;
    case 'profesor':
        $redirectUrl = 'panel-profesor/panel-profesor.php';
        break;
    default:
        $redirectUrl = 'panel-alumno/panel-alumno.php';
        break;
}

echo json_encode([
    'success'  => true,
    'redirect' => $redirectUrl
]);
exit;
?>