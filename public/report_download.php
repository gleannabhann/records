<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if($_SERVER['REQUEST_METHOD'] == "POST")  {
    // output headers so that the file is downloaded rather than displayed
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=data.csv');
    // configuration
    require("../includes/config.php");
    // create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');

    $cxn = open_db_browse();
    $query=$_POST["query"];
    $data = mysqli_query ($cxn, $query) 
        or die ("Couldn't execute query to build report.");
    // prepare and output the column headings
    $fields = mysqli_fetch_fields($data);
    $i=0;
    foreach ($fields as $field) {
        $headers[$i]=$field->name;
        $i++;
    }
    fputcsv($output, $headers);

    while ($row = mysqli_fetch_assoc($data)) {
        fputcsv($output, $row);
    }
}
?>
