
<div class="container">
<?php
// ASSUME: id_person is passed as parameter as ?id=X
// PURPOSE: This script will display the person's information in editable boxes.
//          Upon pressing the submit button the data will be updated in
//             edit_person_update.php
/* connect to the database */
//$cxn = mysqli_connect ("localhost", "oop", "ooppassword","oop")
//or die ("message");
$cxn = mysqli_connect (SERVER,DB_USER,DB_PWD,DATABASE)
or die ("message");

// If person is not logged in they should see a simple No Access message
if (is_logged_in()){
   $id_person = $_GET["id"];
   if ($id_person != "") {
      // Process person
      // 1: Get Data from db, set variables
      // 2: Close php briefly, build form, back to php.
      //    Note: actually updating the infor will be done in edit_person_update.php
   } else {
       // TODO: Error; no person chosen
   }
} else {
    //TODO: Blank out page, explain no access is available.
}
    

?>
<!-- end of php -->
