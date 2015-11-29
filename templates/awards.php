<?php
/* connect to the database */
$cxn = open_db_browse();

echo "<div class='row'><div class='col-md-6 col-md-offset-3'>";


///////////////////////////////////////////////////////////////////////////////
// Main portion of the page
///////////////////////////////////////////////////////////////////////////////
echo "<div class='center-block'>";
include "alpha.php";
echo "</div>";
echo "<div class='center-block'>";
echo form_subtitle("Search on a Partial Name");
    echo '<form role="search" action="search.php" method="get">';
    echo  '<input type="text" class="form-control" '
    . 'placeholder="Search for Name or Award" name="name">';
    echo '<button type="submit" class="btn btn-default">Submit</button>';
    ///////////////////////////////////////////////////////////////////////////////
    // Include the most recent 5 events
    ///////////////////////////////////////////////////////////////////////////////
    echo "<br><br>";
    echo form_subtitle("The 6 most recent events:");
    echo button_link("list_events.php", "List all events in the database");
    $query = "SELECT id_event, name_event, name_group, date_event_start, date_event_stop "
            . "FROM Events, Groups "
            . "WHERE Events.id_group = Groups.id_group "
            . "AND date_event_stop <= curdate() "
            . "ORDER BY date_event_start DESC "
            . "LIMIT 6;";
    $result = mysqli_query ($cxn, $query) or die ("Couldn't execute event_info query");
    
    echo "<table class='table table-condensed table-bordered'>
    <thead>
    <td ><strong>Event</strong></td>
    <td class='text-left'><strong>Hosts</strong></td>
    <td class='text-left'><strong>Dates</strong></td>
    </thead>";
    while ($row = mysqli_fetch_assoc($result)){
        extract($row);
        echo "<tr>";
        echo "<td ><a href='event.php?id=$id_event'>$name_event</a></td>";
        echo "<td >$name_group</td>";
        echo "<td >".date("d-M-Y", strtotime($date_event_start));
        if ($date_event_start != $date_event_stop) {
            echo " --  ".date("d-M-Y", strtotime($date_event_stop));
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
echo "</div>";
echo "</div><!-- ./col-md-6 -->";

///////////////////////////////////////////////////////////////////////////////
// News column on right
///////////////////////////////////////////////////////////////////////////////
echo '<div class="col-md-3 well">';
echo form_subtitle("Most recent awards");
$query = "SELECT Persons.id_person, name_person, name_award, date_award, Awards.id_award "
        . "FROM Persons, Persons_Awards, Awards "
        . "WHERE Persons.id_person=Persons_Awards.id_person "
        . "AND Awards.id_award = Persons_Awards.id_award "
        . "ORDER BY date_award DESC "
        . "LIMIT 20";
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute query");
while ($row = mysqli_fetch_assoc($result)) {
    extract($row);
    echo "<li><a href='person.php?id=$id_person'>"
            . "$name_person</a> received <a href='list.php?award=$id_award'>$name_award</a> on $date_award"
            . "</li>";
}

include "warning.php"; // includes the warning text about paper precedence

echo "<div> <!-- ./col-md-3 --></div> <!-- ./row -->";


mysqli_close ($cxn); /* close the db connection */
?>
