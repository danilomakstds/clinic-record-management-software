<?php
include "connect.php";

$sqlString = "SELECT * FROM `test`";
$rs = mysqli_query($con, $sqlString);
if(mysqli_num_rows($rs) > 0){
    while($objRs = mysqli_fetch_array($rs)){
       $output[] = $objRs;
    }
    $json = json_encode($output);
    print($json);
 } else {
    print(0);
 }
?>