<?php
 
        $servername = "localhost"; 
        $username = "root"; 
        $password = ""; 
        $dbname = "id21836710_heladeras"; 
        
        $con = new mysqli($servername, $username, $password, $dbname);

        if ($con->connect_error) {
            die("Conexión fallida: " . $con->connect_error);
        }
        ?>