<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// Check just in case we got here by accident
if (permissions("Herald")< 3) {
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();    
}
if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
    // We got here through the edit link on person.php
    // echo "Arrived from person.php";
    $id_person = $_GET["id"];
    $id_person_award = $_GET["idpa"];
    $is_deleted = FALSE;
} elseif ((isset($_POST['id'])) && (is_numeric($_POST['id']))) {
    // We got here from form submission and hence will be deleting the info
    // echo "Arrived as form submission";
    $id_person = $_POST['id'];
    $id_person_award = $_POST["idpa"];
    $is_deleted = TRUE;
} else {
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}


$cxn = open_db_browse();
$query = "SELECT  id_person_award, name_person, name_award, "
         . "date_award, name_kingdom "
         . "from Persons, Persons_Awards, Awards, Kingdoms "
         . "WHERE Persons.id_person = Persons_Awards.id_person "
         . "AND Persons_Awards.id_award = Awards.id_award "
         . "AND Awards.id_kingdom = Kingdoms.id_kingdom "
         . "AND Persons.id_person = $id_person "
         . "AND id_person_award=$id_person_award "
         . "ORDER by date_award";
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute query");
if (mysqli_num_rows($result)!=1) {
    echo "Couldn't execute deletion";
    exit_with_footer();
} else {
    $award = mysqli_fetch_array($result);
}
if (!$is_deleted) { // Haven't pressed delete button yet
// Form that basically consists of 2 buttons: delete and cancel
    echo form_title('You are deleting the award of '.$award["name_award"].' to '
        . $award["name_person"].' on '.$award["date_award"].' in '
        . $award["name_kingdom"]);
    echo "<table><tr>"; 
  echo '<td><form action="delete_person_award.php" method="post">';
   echo '<input type="hidden" name="id" value="'.$id_person.'">';
   echo '<input type="hidden" name="idpa" value="'.$id_person_award.'">';
   echo '<input type="submit" value="Delete" name="Delete" class="btn btn-primary">';
   echo '</form></td>';
   echo '<td><form action="edit_person.php" method="get">';
   echo '<input type="hidden" name="id" value="'.$id_person.'">';
   echo '<input type="submit" value="Cancel Deletion" class="btn btn-primary"">';
   echo '</form></td>';
} else { // Have pressed delete button so will be deleting award
    $delete = "DELETE FROM Persons_Awards WHERE id_person_award=$id_person_award";
    $result=update_query($cxn, $delete);
    if ($result !== 1) {echo "Error deleting record: " . mysqli_error($cxn);}

   echo form_title('You have deleted the award of '.$award["name_award"].' to '
        . $award["name_person"].' on '.$award["date_award"].' in '
        . $award["name_kingdom"]);

   echo '<form action="edit_person.php" method="get">';
   echo '<input type="hidden" name="id" value="'.$id_person.'">';
   echo '<input type="submit" value="Return to Edit Person" class="btn btn-primary">';
   echo '</form>';
}

mysqli_close ($cxn); /* close the db connection */
       
?> 
