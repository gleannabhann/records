<?php
// Assumption: this is only loaded from reports.php so don't need to access check again
// Note: cxn is live.

echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
echo '<form action="report_marshal.php" method="post">';
echo form_title("Please select a Marshal's report");

echo '<input type="submit" value="Get Report">';
echo "</form>";
echo "</div></div>";