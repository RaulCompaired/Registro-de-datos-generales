<?php
$host = 'localhost';   
$dbname = 'registroexamen'; 
$username = 'root';          
$password = '';

    $conexion= mysqli_connect($host,$username,$password,$dbname);
    //Para que no se muestren los errores de mysql
    mysqli_report(MYSQLI_REPORT_OFF);

 ?>