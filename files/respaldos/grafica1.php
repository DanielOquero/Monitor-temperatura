<?php
    header('Content-Type: application/javascript');
?>
google.charts.load("current", {packages:["corechart"]});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    var data = google.visualization.arrayToDataTable([
        ['Hora', 'Temperatura'],
        <?php
            include 'files/conexion.php';
        
            if ($con->connect_error) {
                die("Conexión fallida: " . $con->connect_error);
            }
            
            $heladera = 1;
            $tiempo_grafica = 3600;

            // Obtener los registros de las últimas 12 horas
            $sql = "SELECT DATE_FORMAT(fecha_hora, '%Y-%m-%d %H:00:00') as fecha_hora, temperatura
                    FROM temperaturas
                    WHERE heladera_id = $heladera
                    AND fecha_hora >= NOW() - INTERVAL 12 HOUR
                    ORDER BY fecha_hora";
            $result = $con->query($sql);

            // Crear un array asociativo con las horas y temperaturas
            $temperaturas = [];
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $temperaturas[$row['fecha_hora']] = $row['temperatura'];
                }
            }

            // Rellenar los datos para las últimas 12 horas
            $now = new DateTime();
            $now->setTime($now->format('H'), 0, 0); // Ajustar a la hora actual redondeada hacia abajo
            $dataRows = [];
            for ($i = 11; $i >= 0; $i--) {
                $hora = $now->format('H:i');
                $fechaHora = $now->format('Y-m-d H:00:00');
                $temperatura = isset($temperaturas[$fechaHora]) ? $temperaturas[$fechaHora] : 0;
                $dataRows[] = "['" . $hora . "', " . $temperatura . "]";
                $now->modify('-1 hour');
            }

            echo implode(",", $dataRows);

            $con->close();
        ?>
    ]);

    var options = {
        title: 'Temperatura de la Heladera',
        curveType: 'function',
        legend: { position: 'bottom' },
        hAxis: {
            title: 'Hora',
            format: 'HH:mm',
            gridlines: { count: 10 }
        },
        vAxis: {
            title: 'Temperatura (°C)'
        },
        width: '100%',
        height: '100%'
    };

    var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
    chart.draw(data, options);
}

// Script para recargar la página
setTimeout(function(){
    window.location.reload(1);
}, 60000);

// Script para recargar la página
setTimeout(function(){
    window.location.reload(1);
}, 60000);
