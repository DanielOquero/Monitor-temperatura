<?php
include 'conexion.php';

date_default_timezone_set('America/Buenos_Aires');

if ($con) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['temperatureC'])) {
            $temperatura = mysqli_real_escape_string($con, $_POST['temperatureC']);
        } else {
            $temperatura = 0;
        }

        $fecha_hora = date("Y-m-d H:i:s");

        // Crea la consulta SQL para insertar el registro
        $consulta = "INSERT INTO temperaturas (temperatura, fecha_hora, heladera_id) 
                     VALUES ('$temperatura', '$fecha_hora', '$heladera_id')";

        // Ejecuta la consulta
        if (mysqli_query($con, $consulta)) {
            echo "Registro en base de datos OK!";
        } else {
            echo "¡Falla! Registro en BD: " . mysqli_error($con);
        }
    } 
} else {
    echo "¡Falla! Conexión con la base de datos.";
}
?>