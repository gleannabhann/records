<?php

// Query the database
$queryreport=$qshow.$query;
if (DEBUG) {
    log_debug("Report query is:", $queryreport);
}
// Query the database
try {
    $sth = $cxn->prepare($queryreport);
    if (isset($data)) {
        $sth->execute($data);
    } else {
        $sth->execute();
    }
} catch (PDOException $e) {
    $msg = "Couldn't fetch the report.";
    echo "<div class='row'><div class='col-sm-12 col-md-8 col-md-offset-2'>";
    bs_alert($msg, 'danger');
    echo "</div></div>";
    if (DEBUG) {
        $vars = ['query' => $queryreport, 'data' => $data];
        log_debug($msg, $vars, $e);
    }
    // no point in continuing
    exit_with_footer;
}
echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
echo '<form action="/public/report_download.php" method="post">';
//echo form_title("Download Report");
echo '<input type="hidden" name="query" value="'.$qfile.$query.'">';
echo '<input type="submit" value="Download Report as CSV file">';
echo "</form>";
echo "</div></div>";

// Displays a table with sortable columns based on the data stored in $data.
echo form_title($report_name);
$fields = [];
foreach (range(0, $sth->columnCount() - 1) as $i) {
    $fields[] = $sth->getColumnMeta($i);
}
    echo '<table class="sortable table table-condensed table-bordered">';
    echo '<thead>';
        foreach ($fields as $field) {
            echo '<th>'.$field['name'].'</th>';
        }
        echo '</thead>';
    while ($row = $sth->fetch()) {
        echo '<tr>';
        foreach ($row as $field) {
            echo '<td>'.$field.'</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
