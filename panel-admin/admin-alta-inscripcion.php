<?php
session_start();
$conexion = require("../includes/conexion.php");

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "admin") {
    header("Location: ../login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    header("Content-Type: application/json");

    $id_cliente  = $_POST["ID_cliente"] ?? "";
    $id_horarios = $_POST["ID_horarios"] ?? "";

    if ($id_cliente === "" || $id_horarios === "") {
        echo json_encode(["success" => false, "message" => "Elegí un alumno y un horario."]);
        exit;
    }

    // ¿Ya existe una inscripción de ese alumno en ese horario?
    $sql_existe = "SELECT estado FROM Inscripcion WHERE ID_cliente = ? AND ID_horarios = ?";
    $sentencia_existe = mysqli_prepare($conexion, $sql_existe);
    mysqli_stmt_bind_param($sentencia_existe, "ii", $id_cliente, $id_horarios);
    mysqli_stmt_execute($sentencia_existe);
    $existe = mysqli_stmt_get_result($sentencia_existe)->fetch_assoc();

    if ($existe && $existe["estado"] === "activa") {
        echo json_encode(["success" => false, "message" => "Ese alumno ya está inscripto en ese horario."]);
        exit;
    }

    if ($existe) {
        echo json_encode(["success" => false, "message" => "Ese alumno tiene una inscripción cancelada en ese horario. Reactivala desde la lista de Inscripciones."]);
        exit;
    }

    // Verificamos cupo disponible
    $sql_cupo = "SELECT h.cupo_maximo,
                        (SELECT COUNT(*) FROM Inscripcion WHERE ID_horarios = h.ID_horarios AND estado = 'activa') AS ocupados
                 FROM Horarios h WHERE h.ID_horarios = ?";
    $sentencia_cupo = mysqli_prepare($conexion, $sql_cupo);
    mysqli_stmt_bind_param($sentencia_cupo, "i", $id_horarios);
    mysqli_stmt_execute($sentencia_cupo);
    $cupo_info = mysqli_stmt_get_result($sentencia_cupo)->fetch_assoc();

    if (!$cupo_info) {
        echo json_encode(["success" => false, "message" => "El horario elegido no existe."]);
        exit;
    }

    if ($cupo_info["ocupados"] >= $cupo_info["cupo_maximo"]) {
        echo json_encode(["success" => false, "message" => "Ese horario ya alcanzó el cupo máximo de alumnos."]);
        exit;
    }

    try {
        $sql = "INSERT INTO Inscripcion (ID_cliente, ID_horarios) VALUES (?, ?)";
        $sentencia = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($sentencia, "ii", $id_cliente, $id_horarios);
        mysqli_stmt_execute($sentencia);

        echo json_encode(["success" => true, "message" => "El alumno fue inscripto."]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "No se pudo registrar la inscripción."]);
    }
    exit;
}

$alumnos = mysqli_query($conexion, "
    SELECT cl.ID_cliente, p.nombre_persona, p.apellido_persona
    FROM Cliente cl JOIN Persona p ON p.DNI_persona = cl.DNI_persona
    WHERE p.activo = TRUE AND p.rol_persona = 'alumno'
    ORDER BY p.apellido_persona");

$horarios = mysqli_query($conexion, "
    SELECT h.ID_horarios, c.nombre_curso, h.DiaSemana_horarios, h.horaInicio_horarios, h.horaFin_horarios,
           h.cupo_maximo,
           (SELECT COUNT(*) FROM Inscripcion WHERE ID_horarios = h.ID_horarios AND estado = 'activa') AS ocupados
    FROM Horarios h JOIN Curso c ON c.ID_curso = h.ID_curso
    ORDER BY c.nombre_curso, h.DiaSemana_horarios");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscribir alumno - New Ways</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../style.css?v=1.1">
</head>
<body>

<header class="header">
    <div class="header-container">
        <div class="header-left">
            <img src="../img/Logo.png" class="logo">
            <h1>New ways</h1>
        </div>

        <nav class="menu">
            <a href="panel-admin.php">Dashboard</a>
            <a href="admin-alumnos.php">Alumnos</a>
            <a href="admin-profesores.php">Profesores</a>
            <a href="admin-cursos.php">Cursos</a>
            <a href="admin-horarios.php">Horarios</a>
            <a href="admin-inscripciones.php">Inscripciones</a>
            <a href="../logout.php">Cerrar sesión</a>
        </nav>

        <div class="toggle-container">
            <input type="checkbox" id="modoToggle">
            <label for="modoToggle" class="toggle"></label>
        </div>
    </div>
</header>

<section class="form-section">
    <div class="form-card">
        <h2>Inscribir alumno</h2>

        <form id="form-inscripcion" action="admin-alta-inscripcion.php" method="POST"
            data-ajax
            data-pregunta="¿Inscribir al alumno?"
            data-exito="Alumno inscripto correctamente"
            data-redireccion="admin-inscripciones.php">

            <select name="ID_cliente" required>
                <option value="">-- Elegí un alumno --</option>
                <?php while ($a = mysqli_fetch_assoc($alumnos)): ?>
                    <option value="<?= $a['ID_cliente'] ?>"><?= htmlspecialchars($a['nombre_persona'] . " " . $a['apellido_persona']) ?></option>
                <?php endwhile; ?>
            </select>

            <select name="ID_horarios" required>
                <option value="">-- Elegí un curso/horario --</option>
                <?php while ($h = mysqli_fetch_assoc($horarios)):
                    $lleno = $h["ocupados"] >= $h["cupo_maximo"];
                ?>
                    <option value="<?= $h['ID_horarios'] ?>" <?= $lleno ? "disabled" : "" ?>>
                        <?= htmlspecialchars($h['nombre_curso'] . " — " . $h['DiaSemana_horarios'] . " " . $h['horaInicio_horarios'] . "-" . $h['horaFin_horarios']) ?>
                        (<?= $h['ocupados'] ?>/<?= $h['cupo_maximo'] ?><?= $lleno ? " - CUPO LLENO" : "" ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit" class="hero-button">Inscribir</button>
        </form>
    </div>
</section>

<script src="../script.js?v=1.2"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>