<?php
// Assumption: this is only loaded from reports.php so don't need to access check again
// Note: cxn is live.


echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
echo '<form action="report_sites.php" method="post">';
echo form_title("Please select a Sites report");
echo "<table class='table table-condensed table-bordered'>";
echo '<tr><td class="text-right">Report:</td>';
    echo '<td><select name="id_report" >';
    echo '<option value="1">All Active Sites</option>';
    echo '<option value="2">All Active Sites needing verification</option>';
    echo '<option value="3">All Inactive Sites</option>';
    echo '</select></td></tr>';
//echo '<tr><td>Download as a file?</td>';
//    echo '<td><input type="checkbox" name="get_file" value="1">';
//    echo '</td></tr>';
echo "</table>";
echo '<input type="submit" value="Get Report">';
echo "</form>";
echo "</div></div>";