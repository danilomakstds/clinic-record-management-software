<?php
include "connect.php";

mysqli_set_charset($con, "UTF8");

switch ($_GET['type']) {
   case 'all':
      $sqlString = "SELECT * FROM `appointment` WHERE app_status < 2 ORDER BY app_date DESC, app_timeslot ASC";
      if (!empty($_GET['isToday'])) {
         $today = $_GET['isToday'];
         $sqlString = "SELECT * FROM `appointment` WHERE app_date like '%$today%' AND app_status < 2 ORDER BY app_timeslot ASC";
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
         $sqlString = "SELECT * FROM `appointment` ORDER BY app_date DESC, app_timeslot ASC";
         if (!empty($_GET['isToday'])) {
            $today = $_GET['isToday'];
            $sqlString = "SELECT * FROM `appointment` WHERE app_date like '%$today%' ORDER BY app_timeslot ASC";
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
         $sqlString = "SELECT * FROM `appointment` WHERE app_status = 0 ORDER BY app_date DESC, app_timeslot ASC";
         if (!empty($_GET['isToday'])) {
            $today = $_GET['isToday'];
            $sqlString = "SELECT * FROM `appointment` WHERE app_date like '%$today%' AND app_status = 0 ORDER BY app_timeslot ASC";
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
         $sqlString = "SELECT * FROM `appointment` WHERE app_status = 1 ORDER BY app_date DESC, app_timeslot ASC";
         if (!empty($_GET['isToday'])) {
            $today = $_GET['isToday'];
            $sqlString = "SELECT * FROM `appointment` WHERE app_date like '%$today%' AND app_status = 1 ORDER BY app_timeslot ASC";
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
      $sqlString = "SELECT * FROM `appointment` WHERE app_userId = '{$_GET['userId']}' AND app_status < 2 ORDER BY app_date DESC, app_timeslot ASC";
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
      $sqlString = "SELECT * FROM `appointment` WHERE app_userId = '{$_GET['userId']}' AND app_status = 2 ORDER BY app_date DESC, app_timeslot ASC";
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

      $sqlString = "INSERT INTO `appointment` (`id`, `app_userId`, `app_apptype`, `app_timeslot`, `app_date`,
      `app_patientconcerns`, `app_diagnosis`, `app_prescription`, `app_status`, `app_patient_height`,
      `app_patient_weight`, `app_patient_bp`, `app_lmp`, `app_edc`, `app_muac`, `app_temp`, `app_num_living_children`,
      `app_medical_history_illness`, `app_partners_name`, `app_partners_age`, `app_planning_more_children`, `app_baby_status`, `app_household_income`, `date_created`)
      VALUES (NULL,
      '{$app_userId}',
      '{$app_apptype}',
      '{$app_timeslot}',
      '{$app_date}',
      '{$app_patientconcerns}','','',0,0,0,'',
      '','','','',0,'','',0,0,'',0,CURRENT_TIMESTAMP)";
      $rs = mysqli_query($con, $sqlString);
      echo(mysqli_affected_rows($con));
      break;
   case 'addbabydatasheet':
      $userid = $_GET['userid'];
      $sqlString = "INSERT INTO `baby_datasheet`
      (`id`, `baby_userid`, `baby_lbwgi_1`, `baby_lbwgi_2`, `baby_lbwgi_3`, `baby_dpt_1`, `baby_dpt_2`, `baby_dpt_3`,
      `baby_opv_1`, `baby_opv_2`, `baby_opv_3`, `baby_pcv_1`, `baby_pcv_2`, `baby_pcv_3`, `baby_ipv`,
      `baby_lbwgi_1_date`, `baby_lbwgi_2_date`, `baby_lbwgi_3_date`, `baby_dpt_1_date`, `baby_dpt_2_date`,
      `baby_dpt_3_date`, `baby_opv_1_date`, `baby_opv_2_date`, `baby_opv_3_date`, `baby_pcv_1_date`, `baby_pcv_2_date`, `baby_pcv_3_date`, `baby_ipv_date`)
      VALUES (NULL, '{$userid}', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '', '')";
      $rs = mysqli_query($con, $sqlString);
      echo(mysqli_affected_rows($con));
      break;
   case 'updatebabydatasheet':
      $userid = $_GET['userid'];
      $field = $_POST['field'];
      $fielddate = $field.'_date';
      $now = $_POST['now'];
      $isTrue = $_POST['isTrue'];
      $sqlString = "UPDATE `baby_datasheet` SET $field = '{$isTrue}', $fielddate = '{$now}' WHERE `baby_datasheet`.`baby_userid` = '{$userid}'";
      $rs = mysqli_query($con, $sqlString);
      echo(mysqli_affected_rows($con));
      break;
   case 'getbabydatasheetbyuserid':
      $userid = $_GET['userid'];
      $sqlString = "SELECT * FROM `baby_datasheet` WHERE baby_userid = '{$userid}'";
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

      $app_muac = $_POST['app_muac'];
      $app_temp = $_POST['app_temp'];
      $app_medical_history_illness = $_POST['app_medical_history_illness'];
      $app_lmp = $_POST['app_lmp'];
      $app_edc = $_POST['app_edc'];
      $app_partners_name = $_POST['app_partners_name'];
      $app_partners_age = $_POST['app_partners_age'];
      $app_planning_more_children = $_POST['app_planning_more_children'];
      $app_baby_status = $_POST['app_baby_status'];
      $app_household_income = $_POST['app_household_income'];

      $sqlString = "UPDATE `appointment` SET
      `app_patient_height` = '{$app_patient_height}',
      `app_patient_weight` = '{$app_patient_weight}',
      `app_patient_bp` = '{$app_patient_bp}',
      `app_muac` = '{$app_muac}',
      `app_temp` = '{$app_temp}',
      `app_medical_history_illness` = '{$app_medical_history_illness}',
      `app_lmp` = '{$app_lmp}',
      `app_edc` = '{$app_edc}',
      `app_partners_name` = '{$app_partners_name}',
      `app_partners_age` = '{$app_partners_age}',
      `app_planning_more_children` = '{$app_planning_more_children}',
      `app_baby_status` = '{$app_baby_status}',
      `app_household_income` = '{$app_household_income}',
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
   case 'getallrecordsbymonth':
      $sqlString = "SELECT * FROM `appointment` WHERE `app_date` LIKE '{$_GET['month']}%' AND `app_apptype` = '{$_GET['app_apptype']}' AND `app_status` = '2'";
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
}
mysqli_close($con);
?>


