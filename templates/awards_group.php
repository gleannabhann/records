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

$query = "SELECT "
        . "concat('<a href=''person.php?id=',Persons.id_person,'''>',name_person,'</a>') as 'SCA Name', "
        . "concat('<a href=''list.php?award=',Awards.id_award,'''>',Awards.name_award,'</a>') as 'Award', "
        . "Persons_Awards.date_award as 'Date Awarded' "
        . "FROM Persons, Groups, Persons_Awards, Awards "
        . "WHERE Persons.id_group=Groups.id_group "
        . "AND Persons_Awards.id_person = Persons.id_person "
        . "AND Persons_Awards.id_award = Awards.id_award "
        . "AND date_Award >= :start_date "
        . "AND date_award <= :end_date ";
$data = [':start_date' => $start_date, ':end_date' => $end_date];
if ($id_group == -1) {
    $k_query = "SELECT host_kingdom_id FROM Appdata where app_id=1";
    // TODO wrap this in a try catch block
    $sth = $cxn->query($k_query);
    $k_id = $sth->fetch();
    $query = $query . " AND Groups.id_kingdom = :host_kingdom_id ";
    $data[':host_kingdom_id'] = $k_id['host_kingdom_id'];
} else {
    $query = $query . " AND Groups.id_group = :id_group ";
    $data[':id_group'] = $id_group;
}
$query = $query . "ORDER BY date_award;";
$sth = $cxn->prepare($query);
$sth->execute($data);
$fields = [];
foreach (range(0, $sth->columnCount() -1) as $i) {
    $col = $sth->getColumnMeta($i);
    $fields[] = $col['name'];
}
//    echo "<table class='table table-condensed table-bordered'>";
    echo '<table class="sortable table table-condensed table-bordered">';
    echo '<thead>';
        foreach ($fields as $field) {
            echo '<th>'.$field.'</th>';
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
