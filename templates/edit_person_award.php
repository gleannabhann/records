<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (permissions("Herald")< 3) {
    //echo var_dump($_SESSION);
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();    
}

if ((isset($_GET['idpa'])) && (is_numeric($_GET['idpa']))
        & (isset($_GET['id'])) && (is_numeric($_GET['id']))) {
    // We got here through the edit link on person.php
    // echo "Arrived from person.php";
    $id_person_award = $_GET["idpa"];
    $id_person = $_GET["id"];
} elseif ((isset($_POST['idpa'])) && (is_numeric($_POST['idpa']))
        && (isset($_POST['id'])) && (is_numeric($_POST['id']))) {
    // We got here from form submission and hence will be deleting the info
    // echo "Arrived as form submission";
    $id_person_award = $_POST["idpa"];
    $id_person = $_POST["id"];
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
//         . "AND Persons.id_person = $id_person "
         . "AND id_person_award=$id_person_award "
         . "ORDER by date_award";
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute query");
if (mysqli_num_rows($result)!=1) {
    echo "Couldn't execute deletion";
    exit_with_footer();
} else {
    $award = mysqli_fetch_array($result);
}

if (isset($_POST["date_award"]) && is_string($_POST["date_award"])) {
    $date_award = $_POST["date_award"];
} else {
    $date_award = $award["date_award"];
}
$award_info=$award["name_award"]." awarded to ".$award["name_person"].":&nbsp<br>&nbsp";
echo "
<div class='row'>
  <div class='col-md-8 col-md-offset-2'>";

echo form_title("Now editing the date of the award.")."\n";
echo button_link("edit_person.php?id=".$id_person, "Return To Personal Editing Page")
        ."<br><br>";
echo '<form action="edit_person_award.php" method="post">';
echo '<input type="hidden" name="idpa" value="'.$id_person_award.'">';
echo '<input type="hidden" name="id" value="'.$id_person.'">';
echo "<table>";
echo "<tr><td>$award_info</td><td>";
echo '<input type="date" class="date" name="date_award" value="'.$date_award.'">';
echo "<br>(format if no datepicker: yyyy-mm-dd)</td></tr>";
echo "</table>";
echo '<input type="submit" value="Edit Date">';
echo '</form>';

echo "</div><!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs

// Let's see if date has changed and update accordingly
if (isset($_POST["date_award"]) && ($date_award != $award["date_award"])){
    //echo "Now updating the database.<br>";
    $update = "UPDATE Persons_Awards SET date_award='$date_award'"
            . " WHERE id_person_award = $id_person_award";
    //echo "Query is $update<br>";
    $result=update_query($cxn, $update);
    if ($result !== 1) {
        echo "Error updating record: " . mysqli_error($cxn);
    } else {
        echo "Database updated.";
    }
}
mysqli_close($cxn); /* close the db connection */
?>