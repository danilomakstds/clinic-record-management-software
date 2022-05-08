<?php
include "connect.php";

mysqli_set_charset($con, "UTF8");

switch ($_GET['type']) {
   case 'all':
      $sqlString = "SELECT * FROM `schedule`";
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
      break;
   case 'deleteschedule':
      $schedid = $_GET['schedid'];
      $sqlString = "DELETE FROM `schedule` WHERE `schedule`.`slotid` = '{$schedid}'";
      $rs = mysqli_query($con, $sqlString);
      echo(mysqli_affected_rows($con));
      break;
   case 'workdays':
      $sqlString = "SELECT * FROM `workdays`";
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
      break;
   case 'updateworkdays':
      $daysarray = $_POST['daysarray'];
      $sqlString = "UPDATE `workdays` SET
      `daysarray` = '{$daysarray}' WHERE `workdays`.`id` = 1";
      $rs = mysqli_query($con, $sqlString);
      echo(mysqli_affected_rows($con));
      break;
   case 'addnewschedule':
      $timestart = $_POST['timestart'];
      $timeend = $_POST['timeend'];
      $sched = $_POST['sched'];

      $sqlString = "INSERT INTO `schedule`
      (`slotid`, `timestart`, `timeend`, `sched`)
      VALUES (NULL,
      '{$timestart}',
      '{$timeend}',
      '{$sched}')";
      $rs = mysqli_query($con, $sqlString);
      echo(mysqli_affected_rows($con));
      break;
   case 'updateschedule':
      $timestart = $_POST['timestart'];
      $timeend = $_POST['timeend'];
      $sched = $_POST['sched'];
      $slotid = $_GET['slotid'];

      $sqlString = "UPDATE `schedule` SET
      `timestart` = '{$timestart}',
      `timeend` = '{$timeend}',
      `sched` = '{$sched}'
      WHERE `schedule`.`slotid` = '{$slotid}'";
      $rs = mysqli_query($con, $sqlString);
      echo(mysqli_affected_rows($con));
      break;
}
mysqli_close($con);
?>


