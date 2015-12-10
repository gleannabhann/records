<div class="container">
<?php
/* Note: This page displays a list of events.  If permissions are correct, then 
 * the page will also include an "Add Event" button, and the links will go to 
 * edit_event.php rather than event.php.
 */
$cxn = open_db_browse();

$query = "SELECT id_event, name_event, name_group, date_event_start, date_event_stop "
        . "FROM Events, Groups "
        . "WHERE Events.id_group = Groups.id_group "
        . "ORDER BY date_event_start DESC;";
if (DEBUG){
    echo "Event Information Query is:<p>$query<p>";
}
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute event_info query");

echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
echo form_title("A List of All Known Events");
if (permissions("Herald")>= 3){
    echo button_link("add_event.php","Add A New Event");
}
echo "</p>";
echo "<table class='table table-condensed table-bordered'>
<thead>
<td ><strong>Event</strong></td>
<td class='text-left'><strong>Hosts</strong></td>
<td class='text-left'><strong>Dates</strong></td>
</thead>";
while ($row = mysqli_fetch_assoc($result)){
    extract($row);
    echo "<tr>";
    echo "<td ><a href='event.php?id=$id_event'>$name_event";
    echo "</a></td>";
    echo "<td >$name_group</td>";
    echo "<td >".date("d-M-Y", strtotime($date_event_start));
    if ($date_event_start != $date_event_stop) {
        echo " --  ".date("d-M-Y", strtotime($date_event_stop));
    }
    echo "</td>";
    if (permissions("Herald")>= 3){
        echo "<td>".button_link("edit_event.php?id=$id_event", "Edit Event")."</td>";
    }
    echo "</tr>";
}
echo "</table>";
echo "</div><!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs

mysqli_close ($cxn); /* close the db connection */
?>
</div>