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
         . "date_award, name_kingdom, name_event, Events.id_event "
         . "from Persons, Persons_Awards, Awards, Kingdoms, Events "
         . "WHERE Persons.id_person = Persons_Awards.id_person "
         . "AND Persons_Awards.id_award = Awards.id_award "
         . "AND Awards.id_kingdom = Kingdoms.id_kingdom "
         . "AND Persons_Awards.id_event = Events.id_event "
//         . "AND Persons.id_person = $id_person "
         . "AND id_person_award=:id_person_award "
         . "ORDER by date_award";
$data = [':id_person_award' => $id_person_award];
$sth = $cxn->prepare($query);
$sth->execute($data);
if ($sth->rowCount()!=1) {
    echo "Couldn't find award";
    exit_with_footer();
} else {
    $award = $sth->fetch(PDO::FETCH_ASSOC);
}

$query = "SELECT id_event, name_event, date_event_start, date_event_stop "
        . "FROM Events "
        . "ORDER BY date_event_start DESC;";
$sth_events = $cxn->prepare($query);
try {
$sth_events->execute();
} catch (PDOException $e) {
  if (DEBUG) {
      $message = $e->getMessage();
      $code = (int)$e->getCode();
      echo "<p>Error: PDO Exception.</p>";
      echo "<p>Message:</p><p>";
      print_r($message);
      echo "</p><p>Code:</p><p>";
      print_r($code);
    } else {
    echo "Error: Could not find events.";
    exit_with_footer();
    }
}


if (isset($_POST["date_award"]) && is_string($_POST["date_award"])) {
    $date_award = $_POST["date_award"];
} else {
    $date_award = $award["date_award"];
}

if (isset($_POST["id_event"]) && is_numeric($_POST["id_event"])) {
    $id_event = $_POST["id_event"];
} else {
    $id_event = $award["id_event"];
}

$award_info=$award["name_award"]." awarded to ".$award["name_person"] . "";
echo "
<div class='row'>
  <div class='col-md-8 col-md-offset-2'>";

echo form_title("Now editing the date and event of the award.")."\n";
echo button_link("edit_person.php?id=".$id_person, "Return To Personal Editing Page")
        ."<br><br>";
echo '<form action="edit_person_award.php" method="post">';
echo '<input type="hidden" name="idpa" value="'.$id_person_award.'">';
echo '<input type="hidden" name="id" value="'.$id_person.'">';
echo "<table>";
echo "<tr><td width='50%'>$award_info</td><td>";
echo '<input type="date" class="date" name="date_award" value="'.$date_award.'">';
echo "<br>(format if no datepicker: yyyy-mm-dd)</td></tr>";
echo '<tr><td class="text-right">Event:</td><td> <select name="id_event" >';
while ($row= $sth_events->fetch(PDO::FETCH_ASSOC)) {
    echo '<option value="'.$row["id_event"].'"';
    if ($row["id_event"]==$id_event) {
        echo ' selected';
    }
    echo ">".$row["name_event"].
            " (".$row["date_event_start"].
            " - ".$row["date_event_stop"].") </option>";
}
echo '</select></td></tr>';
echo "</table>";
echo '<input type="submit" value="Update Information">';
echo '</form>';

echo "</div><!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs

// Let's see if date has changed and update accordingly
if ((isset($_POST["date_award"]) && ($date_award != $award["date_award"]))
                || (isset($_POST["id_event"]) && ($id_event != $award["id_event"]))) {
    //echo "Now updating the database.<br>";
    $update = "UPDATE Persons_Awards SET date_award=:date_award,"
            . "id_event=:id_event "
            . " WHERE id_person_award = :id_person_award";
    $data = [':date_award' => $date_award, ':id_event' => $id_event, ':id_person_award' => $id_person_award];
    
    if (DEBUG) {
      echo "Query is:<br>";
      print_r($update);
      echo "Data is:<br>";
      print_r($data);
    }
    $result=update_query($cxn, $update, $data);
    if ($result !== 1) {
      echo "<div class='row'><div class='col-md-6 col-md-offset-3'>";
      echo "<div class='center-block alert alert-danger'><p class='center alert alert-danger'>Error updating record: \nPDO::errorInfo():</p>";
      print_r($cxn->errorInfo());
      echo "</div></div></div>";
    } else {
      echo "<div class='row'><div class='col-md-6 col-md-offset-3'>";
      echo "<div class='alert alert-success center-block'>";
      echo "<p class='text-center'>Update Applied.</p></div></div></div>";
    }
} else {
    if (DEBUG) {
        echo "No data changed in form; no update query needed<p>";
    }
}
$cxn = null; /* close the db connection */
?>
