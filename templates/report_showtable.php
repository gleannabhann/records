<?php
// Query the database
$queryreport=$qshow.$query;
if (DEBUG) {
    echo "Report query is: $queryreport<p>";
}
// Query the database
$data = mysqli_query ($cxn, $queryreport) 
        or die ("Couldn't execute query to build report.");

echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
echo '<form action="/public/report_download.php" method="post">';
//echo form_title("Download Report");
echo '<input type="hidden" name="query" value="'.$qfile.$query.'">';
echo '<input type="submit" value="Download Report as CSV file">';
echo "</form>";
echo "</div></div>";

// Displays a table with sortable columns based on the data stored in $data.
    echo form_title($report_name);
    $fields = mysqli_fetch_fields($data);
//    echo "<table class='table table-condensed table-bordered'>";
    echo '<table class="sortable table table-condensed table-bordered">';
    echo '<thead>';
        foreach ($fields as $field) {
            echo '<th>'.$field->name.'</th>';
        }
        echo '</thead>';
    while ($row = mysqli_fetch_assoc($data)) {
        echo '<tr>';
        foreach ($row as $field) {
            echo '<td>'.$field.'</td>';
        }
        echo '</tr>';
    }
    echo '</table>';