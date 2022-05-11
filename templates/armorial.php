<?php

/* connect to the database */
$cxn = open_db_browse();

echo "<div class='row'><div class='col-md-9 col-md-offset-3'>";


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
    echo "</form>";
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
    $result = mysqli_query($cxn, $query) or die("Couldn't execute event_info query");

    echo "<table class='table table-condensed table-bordered'>
    <thead>
    <td ><strong>Event</strong></td>
    <td class='text-left'><strong>Device Image</strong></td>
    <td class='text-left'><strong>Blazon</strong></td>
    </thead>";

    $q_new = "SELECT id_armorial as ia, blazon_armorial as blazon, image_armorial as image, "
            . "fname_armorial as fname, fsize_armorial as fsize, ftype_armorial as ftype "
            . "FROM Armorials";

    if (DEBUG) {
        echo "Query to list devices is: $q_new</br>";
    }
    $new_links = mysqli_query($cxn, $q_new)
            or die("Couldn't execute query to find existing links");
    echo "</div> </div>";
    while ($row = mysqli_fetch_assoc($new_links)) {
        extract($row);
        var_dump($row);
    }

        echo "<tr>";
        echo "<td >";
        if ($image !== false) {
            switch ($ftype) {
                case "image/png"  : echo '<img src="data:image/png;base64,' . $image  . '" />';
                    break;
                case "image/gif"  : echo '<img src="data:image/gif;base64,' . $image  . '" />';
                    break;
                case "image/jpeg" : echo '<img src="data:image/jpeg;base64,' . $image  . '" />';
                    break;
                case "image/jpg"  : echo '<img src="data:image/jpg;base64,' . $image  . '" />';
                    break;
                default:
                    echo "No image";
            }
        }
        echo "</td>";
        echo "<td >$blazon</td>";
        echo "</tr>";

    echo "</table>";


include "warning.php"; // includes the warning text about paper precedence

 echo "<!-- ./row -->";


$cxn = null; /* close the db connection */
