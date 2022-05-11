<?php
/* Creates a downloadable CSV file from the database
 * Source: http://code.stephenmorley.org/php/creating-downloadable-csv-files/
 */

// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, ['Column 1', 'Column 2', 'Column 3']);

// fetch the data
$cxn = open_db_browse();
$query ='SELECT field1,field2,field3 FROM table';
$sth = $cxn->query($query);
// loop over the rows, outputting them
while ($row = $sth->fetch()) {
    fputcsv($output, $row);
}
