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
    $data=$_POST['data'];
    try {
      $sth = $cxn->prepare($query);
      $sth->execute($data);
    } catch (PDOException $e) {
    $error = "Couldn't execute query to build report. ";
    if (DEBUG) {
      $message = $e->getMessage();
      $code = $e->getCode();
      $error = $error . "($message / $code)";
    }
    bs_alert($error, 'warning');
    }

    // prepare and output the column headings
    foreach(range(0, $sth->columnCount() -1) as $i) {
         $fields[] = $sth->getColumnMeta($i);
             }
    $i=0;
    foreach ($fields as $field) {
        $headers[$i]=$field['name'];
        $i++;
    }
    fputcsv($output, $headers);

    while ($row = $sth->fetch()) {
        fputcsv($output, $row);
    }
}
?>
