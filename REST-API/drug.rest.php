<?php
include "connect.php";

mysqli_set_charset($con, "UTF8");

switch ($_GET['type']) {
   case 'all':
      $sqlString = "SELECT * FROM `drug`";
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
   case 'getdrugbyid':
      $drugid = $_GET['drugid'];
      $sqlString = "SELECT * FROM `drug` WHERE `drug`.`id` = '{$drugid}'";
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
   case 'adddrug':
      $drug_name = $_POST['drug_name'];
      $drug_dose = $_POST['drug_dose'];
      $drug_flavor = !empty($_POST['drug_flavor']) ? $_POST['drug_flavor'] : '';
      $drug_route = $_POST['drug_route'];
      $drug_quantity = $_POST['drug_quantity'];
      $drug_expirationdate = $_POST['drug_expirationdate'];

      $sqlString = "INSERT INTO `drug` (`id`, `drug_name`, `drug_dose`, `drug_flavor`, `drug_route`, `drug_quantity`, `drug_expirationdate`, `date_created`)
      VALUES (NULL,
      '{$drug_name}',
      '{$drug_dose}',
      '{$drug_flavor}',
      '{$drug_route}',
      '{$drug_quantity}',
      '{$drug_expirationdate}', CURRENT_TIMESTAMP)";
      $rs = mysqli_query($con, $sqlString);
      echo(mysqli_affected_rows($con));
      break;
   case 'editdrug':
      $drug_name = $_POST['drug_name'];
      $drug_dose = $_POST['drug_dose'];
      $drug_flavor = !empty($_POST['drug_flavor']) ? $_POST['drug_flavor'] : '';
      $drug_route = $_POST['drug_route'];
      $drug_quantity = $_POST['drug_quantity'];
      $drug_expirationdate = $_POST['drug_expirationdate'];
      $drugid = $_GET['drugid'];

      $sqlString = "UPDATE `drug` SET
      `drug_name` = '{$drug_name}',
      `drug_dose` = '{$drug_dose}',
      `drug_flavor` = '{$drug_flavor}',
      `drug_route` = '{$drug_route}',
      `drug_quantity` = '{$drug_quantity}',
      `drug_expirationdate` = '{$drug_expirationdate}' WHERE `drug`.`id` = '{$drugid}'";
      $rs = mysqli_query($con, $sqlString);
      echo(mysqli_affected_rows($con));
      break;
   case 'updatedrugquantity':
      $drugid = $_GET['drugid'];
      $drug_quantity = $_GET['drug_quantity'];
      $sqlString = "UPDATE `drug` SET `drug_quantity` = '{$drug_quantity}' WHERE `drug`.`id` = '{$drugid}'";
      $rs = mysqli_query($con, $sqlString);
      echo(mysqli_affected_rows($con));
      break;
   case 'deletedrug':
      $drugid = $_GET['drugid'];
      $sqlString = "DELETE FROM `drug` WHERE `drug`.`id` = '{$drugid}'";
      $rs = mysqli_query($con, $sqlString);
      echo(mysqli_affected_rows($con));
      break;
   case 'recordstock':
      $drugid = $_GET['drugid'];
      $quantity = $_GET['quantity'];
      $sqlString = "INSERT INTO `drug_quantity_tracker` (`id`, `drugid`, `quantity`, `date_created`)
      VALUES (NULL, '{$drugid}', '{$quantity}', current_timestamp())";
      $rs = mysqli_query($con, $sqlString);
      echo(mysqli_affected_rows($con));
      break;
   case 'getallrecords':
      $sqlString = "SELECT id, drugid, sum(quantity) as sum_quantity FROM `drug_quantity_tracker` GROUP BY drugid";
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


