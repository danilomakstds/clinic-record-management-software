<?php
include "connect.php";

mysqli_set_charset($con, "UTF8");

switch ($_GET['type']) {
   case 'all':
      $sqlString = "SELECT * FROM `appointment` WHERE app_status < 2";
      if (!empty($_GET['isToday'])) {
         $today = $_GET['isToday'];
         $sqlString = "SELECT * FROM `appointment` WHERE app_date like '%$today%' AND app_status < 2";
      }
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
   case 'everything':
         $sqlString = "SELECT * FROM `appointment`";
         if (!empty($_GET['isToday'])) {
            $today = $_GET['isToday'];
            $sqlString = "SELECT * FROM `appointment` WHERE app_date like '%$today%'";
         }
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
   case 'nursesqueue':
         $sqlString = "SELECT * FROM `appointment` WHERE app_status = 0";
         if (!empty($_GET['isToday'])) {
            $today = $_GET['isToday'];
            $sqlString = "SELECT * FROM `appointment` WHERE app_date like '%$today%' AND app_status = 0";
         }
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
   case 'doctorsqueue':
         $sqlString = "SELECT * FROM `appointment` WHERE app_status = 1";
         if (!empty($_GET['isToday'])) {
            $today = $_GET['isToday'];
            $sqlString = "SELECT * FROM `appointment` WHERE app_date like '%$today%' AND app_status = 1";
         }
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
   case 'allbyId':
      $sqlString = "SELECT * FROM `appointment` WHERE app_userId = '{$_GET['userId']}' AND app_status < 2";
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
   case 'alladdreesedbyId':
      $sqlString = "SELECT * FROM `appointment` WHERE app_userId = '{$_GET['userId']}' AND app_status = 2";
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
   case 'addappointment':
      $app_userId = $_POST['app_userId'];
      $app_apptype = $_POST['app_apptype'];
      $app_timeslot = $_POST['app_timeslot'];
      $app_date = $_POST['app_date'];
      $app_patientconcerns = $_POST['app_patientconcerns'];

      $sqlString = "INSERT INTO `appointment`
      (`id`, `app_userId`, `app_apptype`, `app_timeslot`, `app_date`, `app_patientconcerns`, `app_diagnosis`, `app_prescription`, `app_status`,
      `app_patient_height`, `app_patient_weight`, `app_patient_bp`, `date_created`)
      VALUES (NULL,
      '{$app_userId}',
      '{$app_apptype}',
      '{$app_timeslot}',
      '{$app_date}',
      '{$app_patientconcerns}','','',0,0,0,'',CURRENT_TIMESTAMP)";
      $rs = mysqli_query($con, $sqlString);
      echo(mysqli_affected_rows($con));
      break;
   case 'adddiagnosis':
      $app_diagnosis = $_POST['app_diagnosis'];
      $app_prescription = $_POST['app_prescription'];
      $appointmentId = $_GET['appointmentId'];

      $sqlString = "UPDATE `appointment` SET
      `app_diagnosis` = '{$app_diagnosis}',
      `app_prescription` = '{$app_prescription}',
      `app_status` = 2
      WHERE `appointment`.`id` = '{$appointmentId}'";
      $rs = mysqli_query($con, $sqlString);
      echo(mysqli_affected_rows($con));
      break;
   case 'addinitialcheckup':
      $app_patient_height = $_POST['app_patient_height'];
      $app_patient_weight = $_POST['app_patient_weight'];
      $app_patient_bp = $_POST['app_patient_bp'];
      $appointmentId = $_GET['appointmentId'];

      $sqlString = "UPDATE `appointment` SET
      `app_patient_height` = '{$app_patient_height}',
      `app_patient_weight` = '{$app_patient_weight}',
      `app_patient_bp` = '{$app_patient_bp}',
      `app_status` = 1
      WHERE `appointment`.`id` = '{$appointmentId}'";
      $rs = mysqli_query($con, $sqlString);
      echo(mysqli_affected_rows($con));
      break;
   case 'gettimeslotanddate':
      $sqlString = "SELECT app_timeslot, app_date FROM `appointment`";
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
   case 'deleteappointment':
      $appid = $_GET['appid'];
      $sqlString = "DELETE FROM `appointment` WHERE `appointment`.`id` = '{$appid}'";
      $rs = mysqli_query($con, $sqlString);
      echo(mysqli_affected_rows($con));
      break;
   case 'updateappointment':
      $app_type = $_POST['app_type'];
      $app_timeslot = $_POST['app_timeslot'];
      $app_date = $_POST['app_date'];
      $app_id = $_GET['app_id'];

      $sqlString = "UPDATE `appointment` SET
      `app_apptype` = '{$app_type}',
      `app_timeslot` = '{$app_timeslot}',
      `app_date` = '{$app_date}' WHERE `appointment`.`id` = '{$app_id}'";
      $rs = mysqli_query($con, $sqlString);
      echo(mysqli_affected_rows($con));
      break;
}
mysqli_close($con);
?>


