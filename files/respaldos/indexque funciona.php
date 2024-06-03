<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Última Temperatura</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="files/uploads/favicon.png"/>
    <style>
        .text-justify {
            text-align: justify;
        }
        .card-body h1 {
            font-size: 3rem;
        }
    </style>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load("current", {packages:["corechart"]});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Hora', 'Temperatura'],
                <?php
                    include 'conexion.php';
                
                    if ($con->connect_error) {
                        die("Conexión fallida: " . $con->connect_error);
                    }

                    $sql = "SELECT DATE_FORMAT(fecha_hora, '%Y-%m-%d %H:%i') as fecha_hora, temperatura
                            FROM temperaturas
                            WHERE heladera_id = '1'
                            GROUP BY UNIX_TIMESTAMP(fecha_hora) DIV 600
                            ORDER BY fecha_hora DESC
                            LIMIT 30";
                    $result = $con->query($sql);

                    if ($result->num_rows > 0) {
                        $dataRows = [];
                        while($row = $result->fetch_assoc()) {
                            $dateTime = new DateTime($row["fecha_hora"]);
                            $hora = $dateTime->format('H:i');
                            $dataRows[] = "['" . $hora . "', " . $row["temperatura"] . "]";
                        }
                        echo implode(",", $dataRows);
                    } else {
                        echo "['No data', 0]";
                    }

                    $con->close();
                ?>
            ]);

            var options = {
                title: 'Historial de Temperatura de la Heladera',
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

        // Script para recargar la pagina
        setTimeout(function(){
            window.location.reload(1);
        }, 60000);
    </script>
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="https://www.practikaclimatizacion.com.ar/">
                <img src="files/uploads/xone-logo@2x.png" alt="Logo" width="172" height="48">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contacto</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container mt-3">
            <div class="card">
                <div class="card-header">
                    Temperatura Registrada
                </div>
                <div class="row no-gutters">
                    <div class="card-body col-md-6 text-center">
                        <?php
                            $conn = new mysqli($servername, $username, $password, $dbname);
                            if ($conn->connect_error) {
                                die("Conexión fallida: " . $conn->connect_error);
                            }
                            $sql = "SELECT heladera_id, temperatura, fecha_hora FROM temperaturas ORDER BY fecha_hora DESC LIMIT 1";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<h2 class='card-title'>Heladera: " . $row["heladera_id"]. "</h2>";
                                    echo "<p class='card-text'>Fecha y Hora: " . $row["fecha_hora"]. "</p>";
                                }
                            } else {
                                echo "<p class='card-text'>No se encontraron datos.</p>";
                            }
                        ?>
                    </div>
                    <div class="card-body col-md-6 text-center">
                        <?php
                            $sql = "SELECT temperatura FROM temperaturas ORDER BY fecha_hora DESC LIMIT 1";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<h1 class='display-1'>" . $row["temperatura"]. "°C</h1>";
                                }
                            } else {
                                echo "<p class='card-text'>No se encontraron datos.</p>";
                            }
                            $conn->close();
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div id="chart_div" style="width: 100%; height: 500px;" class="mt-3"></div>

        <div class="container">
            <div class="row d-flex justify-content-center mt-3 p-3 bg-dark text-white text-justify">
                <div class="col-lg-12 text-center">
                    <img src="files/uploads/xone-logo-footer@2x.png" alt="Footer Logo" width="60" height="60" class="mb-2">
                </div>
                <div class="col-lg-12 text-center mt-3">
                    <p>Copyright © 2024 - Practika Climatización - by 
                        <a href="https://www.linkedin.com/in/daniel-oquero-perez-191905190/" class="text-white">Daniel Oquero</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>