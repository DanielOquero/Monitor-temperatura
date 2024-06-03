<?php
include 'conexion.php';

//El script se ejecuta indefinidamente
set_time_limit(0);
date_default_timezone_set('America/Buenos_Aires');

if ($con) {
    
    while (true) {
        if (isset($_POST['temperatureC'])) {
            $temperatura = mysqli_real_escape_string($con, $_POST['temperatureC']);
        } else {
            $temperatura = 0;
        }
        
        // ID de la heladera (puedes cambiarlo según tu aplicación)
        $heladera_id = 1;

        // Obtiene la fecha y hora actual
        $fecha_hora = date("Y-m-d H:i:s");

        // Crea la consulta SQL para insertar el registro
        $consulta = "INSERT INTO temperaturas (temperatura, fecha_hora, heladera_id) 
                     VALUES ('$temperatura', '$fecha_hora', '$heladera_id')";

        // Ejecuta la consulta
        $resultado = mysqli_query($con, $consulta);

        if ($resultado) {
            echo "Registro en base de datos OK! <br>";
        } else {
            echo "¡Falla! Registro en BD: " . mysqli_error($con) . "<br>";
        }

        // Espera 60 segundos antes de la próxima iteración
        sleep(60);
    }
} else {
    echo "¡Falla! Conexión con la base de datos.";
}
?>
