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
    $is_deleted = false;
} elseif ((isset($_POST['id'])) && (is_numeric($_POST['id']))) {
    // We got here from form submission and hence will be deleting the info
    // echo "Arrived as form submission";
    $id_person = $_POST['id'];
    $id_person_award = $_POST["idpa"];
    $is_deleted = true;
} else {
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}

$query = "SELECT  id_person_award, name_person, name_award, "
         . "date_award, name_kingdom "
         . "from Persons, Persons_Awards, Awards, Kingdoms "
         . "WHERE Persons.id_person = Persons_Awards.id_person "
         . "AND Persons_Awards.id_award = Awards.id_award "
         . "AND Awards.id_kingdom = Kingdoms.id_kingdom "
         . "AND Persons.id_person = :id_person "
         . "AND id_person_award = :id_person_award "
         . "ORDER by date_award";
$data = [':id_person' => $id_person, ':id_person_award' => $id_person_award];
try {
    $sth = $cxn->prepare($query);
    $sth->execute($data);
} catch (PDOException $e) {
    echo "Couldn't find the targeted record";
    exit_with_footer();
}
    $award = $sth->fetch();

if (!$is_deleted) { // Haven't pressed delete button yet
    // Form that basically consists of 2 buttons: delete and cancel
    echo "<div class='row'><div class='col-sm-12 col-md-8 col-md-offset-2'>";
    echo '<h1 class="text-center"><small>You are deleting the award of</small><br/><strong>'.$award["name_award"].'</strong><br/><small>to <strong>'
        . $award["name_person"].'</strong> on <strong>'.$award["date_award"].'</strong> in <strong>'
        . $award["name_kingdom"]."</strong></small></h1>";
    echo "</div></div>";
    // TODO convert this table to BS grid, center the buttons, and give them
    // some margins so they're not squished. Give them green/red coloring to
    // visually differentiate them
    echo "<div class='row'><div class='col-sm-12 col-md-4 col-md-offset-4'>";
    echo '<form class="form" action="delete_person_award.php" method="post">';
    echo '<div class="form-group">';
    echo '<input type="hidden" name="id" value="'.$id_person.'">';
    echo '<input type="hidden" name="idpa" value="'.$id_person_award.'">';
    echo '<input type="submit" value="Delete" name="Delete" class="btn btn-primary btn-block">';
    echo "</div>";
    echo '</form>';
    echo '<form class="form" action="edit_person.php" method="get">';
    echo "<div class='form-group'>";
    echo '<input type="hidden" name="id" value="'.$id_person.'">';
    echo '<input type="submit" value="Cancel Deletion" class="btn btn-primary btn-block">';
    echo '</div>';
    echo '</form>';
    echo "</div></div>";
} else { // Have pressed delete button so will be deleting award
    $delete = "DELETE FROM Persons_Awards WHERE id_person_award=:id_person_award";
    $data = [':id_person_award' => $id_person_award];
    try {
        $result=update_query($cxn, $delete, $data);
        $msg ='You have deleted the award of '.$award["name_award"].' to '
        . $award["name_person"].' on '.$award["date_award"].' in '
        . $award["name_kingdom"];
        echo "<div class='row'><div class='col-sm-12 col-md-8 col-md-offset-2'>";
        bs_alert($msg, 'success');
        echo "</div></div>";
    } catch (PDOException $e) {
        $msg = "Could not delete the record.";
        echo "<div class='row'><div class='col-sm-12 col-md-8 col-md-offset-2'>";
        bs_alert($msg, 'danger');
        echo "</div></div>";
    }
    echo "<div class='row'><div class='col-sm-12 col-md-8 col-md-offset-2'>";
    echo '<form action="edit_person.php" method="get">';
    echo '<input type="hidden" name="id" value="'.$id_person.'">';
    echo '<input type="submit" value="Return to Edit Person" class="btn btn-primary">';
    echo '</form>';
    echo "</div></div>";
}
