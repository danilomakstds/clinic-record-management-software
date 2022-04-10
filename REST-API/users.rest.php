<?php
include "connect.php";

mysqli_set_charset($con, "UTF8");

switch ($_GET['type']) {
   case 'all':
      $sqlString = "SELECT * FROM `users`";
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
   case 'nameonly':
      $sqlString = "SELECT id, user_firstname, user_lastname, user_middlename FROM `users` WHERE id = '{$_GET['userId']}'";
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
   case 'allemails':
         $sqlString = "SELECT user_email FROM `users`";
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
   case 'getallpatients':
         $sqlString = "SELECT * FROM `users` WHERE user_level = 0";
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
   case 'login':
      $user_email = $_POST['user_email'];
      $user_password = $_POST['user_password'];
      $sqlString = "SELECT * FROM `users` WHERE user_password = '{$user_password}' AND user_email = '{$user_email}'";
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
   case 'addpatient':
      $user_email = $_POST['user_email'];
      $user_password = $_POST['user_password'];
      $user_firstname = $_POST['user_firstname'];
      $user_lastname = $_POST['user_lastname'];
      $user_middlename = $_POST['user_middlename'];
      $user_sex = $_POST['user_sex'];
      $user_address = $_POST['user_address'];
      $user_city = $_POST['user_city'];
      $user_province = $_POST['user_province'];
      $user_contactnum = $_POST['user_contactnum'];
      $user_dob = $_POST['user_dob'];
      $user_maritalstatus = $_POST['user_maritalstatus'];
      
      if (empty($_POST['user_suffix'])) {
         $sqlString = "INSERT INTO `users` (`id`, `user_email`, `user_password`, `user_level`, `user_firstname`, `user_lastname`, `user_middlename`, `user_suffix`, `user_sex`, `user_address`, `user_city`, `user_province`, `user_contactnum`, `user_dob`, `user_maritalstatus`, `date_created`)
            VALUES (NULL,
            '{$user_email}',
            '{$user_password}',
            '0',
            '{$user_firstname}',
            '{$user_lastname}',
            '{$user_middlename}',
            '',
            '{$user_sex}',
            '{$user_address}',
            '{$user_city}',
            '{$user_province}',
            '{$user_contactnum}',
            '{$user_dob}',
            '{$user_maritalstatus}', CURRENT_TIMESTAMP)";
            $rs = mysqli_query($con, $sqlString);
            echo(mysqli_affected_rows($con));
      } else {
         $user_suffix = $_POST['user_suffix'];
         $sqlString = "INSERT INTO `users` (`id`, `user_email`, `user_password`, `user_level`, `user_firstname`, `user_lastname`, `user_middlename`, `user_suffix`, `user_sex`, `user_address`, `user_city`, `user_province`, `user_contactnum`, `user_dob`, `user_maritalstatus`, `date_created`)
            VALUES (NULL,
            '{$user_email}',
            '{$user_password}',
            '0',
            '{$user_firstname}',
            '{$user_lastname}',
            '{$user_middlename}',
            '{$user_suffix}',
            '{$user_sex}',
            '{$user_address}',
            '{$user_city}',
            '{$user_province}',
            '{$user_contactnum}',
            '{$user_dob}',
            '{$user_maritalstatus}', CURRENT_TIMESTAMP)";
            $rs = mysqli_query($con, $sqlString);
            echo(mysqli_affected_rows($con));
      }
      
      break;
   case 'updatepatient':
      $user_email = $_POST['user_email'];
      $user_firstname = $_POST['user_firstname'];
      $user_lastname = $_POST['user_lastname'];
      $user_middlename = $_POST['user_middlename'];
      $user_sex = $_POST['user_sex'];
      $user_address = $_POST['user_address'];
      $user_city = $_POST['user_city'];
      $user_province = $_POST['user_province'];
      $user_contactnum = $_POST['user_contactnum'];
      $user_dob = $_POST['user_dob'];
      $user_maritalstatus = $_POST['user_maritalstatus'];
      $user_suffix = $_POST['user_suffix'];
      $userid = $_GET['userid'];

      $sqlString = "UPDATE `users` SET
      `user_email` = '{$user_email}',
      `user_firstname` = '{$user_firstname}',
      `user_lastname` = '{$user_lastname}',
      `user_middlename` = '{$user_middlename}',
      `user_suffix` = '{$user_suffix}',
      `user_sex` = '{$user_sex}',
      `user_address` = '{$user_address}',
      `user_city` = '{$user_city}',
      `user_province` = '{$user_province}',
      `user_contactnum` = '{$user_contactnum}',
      `user_dob` = '{$user_dob}',
      `user_maritalstatus` = '{$user_maritalstatus}' WHERE id = '{$userid}'";
      $rs = mysqli_query($con, $sqlString);
      echo(mysqli_affected_rows($con));
      break;
   case 'deletepatient':
      $userId = $_GET['userId'];
      $sqlString = "DELETE FROM `users` WHERE `users`.`id` = '{$userId}'";
      $rs = mysqli_query($con, $sqlString);
      echo(mysqli_affected_rows($con));
      break;
}
mysqli_close($con);
?>


