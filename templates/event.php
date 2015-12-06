<div class="container">
<?php

if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
    // We got here through a search link or directly link on list_events.php
    $id_event = $_GET["id"];
 } else {
    echo '<p class="error"> This page has been accessed in error; need to select an event.</p>';
    exit_with_footer();
}

/* connect to the database */
$cxn = open_db_browse();

$query = "SELECT name_event, date_event_start, date_event_stop, name_group, id_site "
        . "FROM Events, Groups "
        . "WHERE id_event=$id_event "
        . "AND Events.id_group= Groups.id_group;";
if (DEBUG){
    echo "Event Information Query is:<p>$query<p>";
}
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute event_info query");
$event_info=mysqli_fetch_assoc($result);  
extract($event_info);
if ($id_site<0) {
    if (DEBUG) {echo "No Known Site<p>";}
    $name_site="Unknown";
} else {
    $query="SELECT name_site FROM Sites where id_site=$id_site";
    $result = mysqli_query ($cxn, $query) or die ("Couldn't execute site name query");
    $tmp=mysqli_fetch_assoc($result);
    $name_site = $tmp["name_site"];
}

/* Display the known information of the event */

echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
echo form_title("$name_event");
echo form_subtitle("Hosted by $name_group from $date_event_start to $date_event_stop");
if (!is_null($id_site)){
    echo form_subtitle("Held at $name_site");
}
if (permissions("Herald")>=3 ){
    echo button_link("edit_event.php?id=$id_event", "Edit Event Information");
}
/* Display Known Award Recipients at this event */
$query = "SELECT Persons.id_person, Awards.id_award, name_person, name_award "
        . "FROM Persons, Awards, Persons_Awards "
        . "WHERE Persons.id_person = Persons_Awards.id_person "
        . "AND Awards.id_award = Persons_Awards.id_award "
        . "AND Persons_Awards.id_event = $id_event;";
if (DEBUG) { echo "<p>The Recipients Query is:<p>$query";}
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute recipients query");
echo form_subtitle("Award Recipients At $name_event");
$matches = $result->num_rows;
if ($matches > 0) {
    echo "<table class='table table-condensed table-bordered'>
    <thead>
    <td ><strong>Recipient</strong></td>
    <td class='text-left'><strong>Award</strong></td>
    </thead>";
    while ($row = mysqli_fetch_assoc($result)){
        extract($row);
        echo "<tr>";
        echo "<td ><a href='person.php?id=$id_person'>$name_person</a></td>";
        echo "<td class='text-left'><a href='list.php?award=$id_award'>$name_award</a></td>";
        echo "</tr>";
    }
    echo "</table><p>";
} else {
    echo "<p>Currently no recipients known for this event.<p>";
}
echo "</div><!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs


if (permissions("Herald")>= 3) {
/* Now let's list potential recipients for the herald's eyes only */
    echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
    echo form_subtitle("People Who May Have Received This Award At This Event");
    echo form_subtitle("(due to the date of the Award)");
    $query = "SELECT Persons.id_person, name_person, name_award, Awards.id_award "
            . "FROM Persons, Awards, Persons_Awards "
            . "WHERE id_event=-1 "
            . "AND date_award >= '$date_event_start' "
            . "AND date_award <= '$date_event_stop' "
            . "AND Persons.id_person = Persons_Awards.id_person "
            . "AND Awards.id_award = Persons_Awards.id_award";
    if (DEBUG){echo "Potential Recipients query is:<p>$query";}
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute potential recipients query");
echo "<table class='table table-condensed table-bordered'>
<thead>
<td ><strong>Recipient</strong></td>
<td class='text-left'><strong>Award</strong></td>
</thead>";
while ($row = mysqli_fetch_assoc($result)){
    extract($row);
    echo "<tr>";
    echo "<td ><a href='edit_person.php?id=$id_person'>$name_person</a></td>";
    echo "<td class='text-left'><a href='list.php?award=$id_award'>$name_award</a></td>";
    echo "</tr>";
}
echo "</table><p>";
echo "</div><!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs
}


mysqli_close ($cxn); /* close the db connection */
?>
</div>