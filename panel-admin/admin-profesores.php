<?php
session_start();
$conexion = require("../includes/conexion.php");

if (!isset($_SESSION["dni"]) || $_SESSION["rol"] !== "admin") {
    header("Location: ../login.html");
    exit;
}

$sql = "SELECT p.DNI_persona, p.nombre_persona, p.apellido_persona, p.email_persona, p.activo,
               pr.disponibilidad, pr.cargaHoraria
        FROM Persona p
        JOIN Profesores pr ON pr.DNI_persona = p.DNI_persona
        WHERE p.rol_persona = 'profesor'
        ORDER BY p.apellido_persona";
$resultado = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profesores - New Ways</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link rel="stylesheet" href="../style.css?v=1.1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
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
            <a href="../logout.php">Cerrar sesión</a>
        </nav>

        <div class="toggle-container">
            <input type="checkbox" id="modoToggle">
            <label for="modoToggle" class="toggle"></label>
        </div>
    </div>
</header>


<section class="form-section">

    <div class="form-card admin-table-card">

        <div class="admin-card-header">
            <h2>Profesores</h2>
            <a href="admin-alta-profesor.php" class="hero-button" style="text-decoration: none;">
                + Agregar profesor
            </a>
        </div>

        <div class="admin-table-wrapper">

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>DNI</th>
                        <th>Email</th>
                        <th>Disponibilidad</th>
                        <th>Carga horaria</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila["nombre_persona"]) ?></td>
                        <td><?= htmlspecialchars($fila["apellido_persona"]) ?></td>
                        <td><?= htmlspecialchars($fila["DNI_persona"]) ?></td>
                        <td><?= htmlspecialchars($fila["email_persona"]) ?></td>
                        <td><?= htmlspecialchars($fila["disponibilidad"] ?? "-") ?></td>
                        <td><?= htmlspecialchars($fila["cargaHoraria"] ?? "-") ?></td>
                        <td><?= $fila["activo"] ? "Activo" : "Inactivo" ?></td>
                        <td>
                            <a href="admin-editar-profesor.php?dni=<?= urlencode($fila["DNI_persona"]) ?>">
                                Editar
                            </a>
                            |
                            <button 
                                type="button" 
                                class="btn-accion" 
                                style="background:none; border:none; color:inherit; cursor:pointer; font-size:inherit; padding:0;"
                                onclick="confirmarToggleGenerico('<?= $fila['DNI_persona'] ?>', '<?= htmlspecialchars($fila['nombre_persona'] . ' ' . $fila['apellido_persona'], ENT_QUOTES) ?>', <?= $fila['activo'] ? 'true' : 'false' ?>, 'admin-toggle-profesor.php', 'profesor')">
                                <?= $fila["activo"] ? "Dar de baja" : "Reactivar" ?>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        </div>

    </div>

</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../script.js?v=1.1"></script>
</body>
</html>