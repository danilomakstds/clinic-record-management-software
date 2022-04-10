<?php
    $con = mysqli_connect("localhost","root","");
    mysqli_select_db($con, "crms");

    // $con = mysqli_connect("localhost","maksdtstpas","Mj5t4JyX2muqakv");
    // mysqli_select_db($con, "maksdtstpas");

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
?>