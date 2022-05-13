<div class="container">
<?php
/* Note: This page displays a list of events.  If permissions are correct, then
 * the page will also include an "Add Event" button, and the links will go to
 * edit_event.php rather than event.php.
 */

$query = "SELECT id_event, name_event, name_group, date_event_start, date_event_stop "
        . "FROM Events, Groups "
        . "WHERE Events.id_group = Groups.id_group "
        . "ORDER BY date_event_start DESC;";
try {
    $sth = $cxn->query($query);
} catch (PDOException $e) {
    $message = "list_events.php Error querying for list of events.";
    $datestamp = date("Y-m-d H:i:s");
    $exc = ['exc_msg' => $e->getMessage(), 'exc_code' => $e->getCode()];
    $vars = ['date' => $datestamp, 'query' => $query, 'exc' => $exc]; // etc, etc
  $arr = ['date' => $datestamp, 'message' => $msg, 'vars' => $vars]; // single array with date, message and vars
  $message = json_encode($arr) . "\n"; //convert it to json for inter-operability
  if (DEBUG) {
      error_log($message, 3, DEBUG_DEST); // post to the debug log
  }
    $f_msg = "I couldn't fetch the list of events.";
    echo "<div class='row'><div class='col-sm-12 col-md-8 col-md-offset-2'>";
    bs_alert($f_msg, 'danger');
    echo "</div></div>";
    // error_log($message, 2, DEV_EMAIL_DEST); // dev emailer, for later
}



echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
echo form_title("A List of All Known Events");
if (permissions("Herald")>= 3) {
    echo button_link("add_event.php", "Add A New Event");
}
echo "</p>";
echo "<table class='table table-condensed table-bordered'>
<thead>
<td ><strong>Event</strong></td>
<td class='text-left'><strong>Hosts</strong></td>
<td class='text-left'><strong>Dates</strong></td>
</thead>";
while ($row = $sth->fetch()) {
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
    if (permissions("Herald")>= 3) {
        echo "<td>".button_link("edit_event.php?id=$id_event", "Edit Event")."</td>";
    }
    echo "</tr>";
}
echo "</table>";
echo "</div><!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs


?>
</div>
