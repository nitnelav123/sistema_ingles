<?php

$conexion = mysqli_connect(
    "localhost",
    "root",
    "",
    "new_ways"
);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

return $conexion;

?>