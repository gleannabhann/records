<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST["start_date"]) 
            && is_string($_POST["start_date"])
            && strlen($_POST["start_date"] > 0)) {
         $start_date = $_POST["start_date"];
    } else {
    echo form_subtitle('Must include Beginning of Time Period to list all awards');
    exit_with_footer();
    }
    if (isset($_POST["end_date"]) 
            && is_string($_POST["end_date"])
            && strlen($_POST["end_date"] > 0)) {
         $end_date = $_POST["end_date"];
    } else {
    echo form_subtitle('Must include End of Time Period to list all awards');
    exit_with_footer();
    }
    if (isset($_POST["id_group"]) && is_numeric($_POST["id_group"])) {
        $id_group = $_POST["id_group"];
    } else {
        $id_group=-1;
    }
} else {
    echo form_subtitle("Page accessed in error.");
    exit_with_footer();
} 
$cxn = open_db_browse();

if (DEBUG) {
    echo "We will be working with timerange $start_date to $end_date, and group $id_group<br>";
}

$query = "SELECT "
        . "concat('<a href=''person.php?id=',Persons.id_person,'''>',name_person,'</a>') as 'SCA Name', "
        . "concat('<a href=''list.php?award=',Awards.id_award,'''>',Awards.name_award,'</a>') as 'Award', "
        . "Persons_Awards.date_award as 'Date Awarded' "
        . "FROM Persons, Groups, Persons_Awards, Awards "
        . "WHERE Persons.id_group=Groups.id_group "
        . "AND Persons_Awards.id_person = Persons.id_person "
        . "AND Persons_Awards.id_award = Awards.id_award "
        . "AND date_Award >= '$start_date' "
        . "AND date_award <= '$end_date' ";
if ($id_group == -1) {
    $query = $query . " AND Groups.id_kingdom = ".HOST_KINGDOM_ID." ";
} else {
        $query = $query . " AND Groups.id_group = $id_group ";
}
$query = $query . "ORDER BY date_award;";

if (DEBUG) {
    echo "Awards query is:</br>$query";
}

$data = mysqli_query ($cxn, $query) 
        or die ("Couldn't execute query to build report.");
//    echo form_title($report_name);
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