<?php
// This page links up the amorials and is available on to the Ruby Herald
// Set up search filters

echo '<form action="edit_person.php" method="post">';
echo form_title("Adding Armorial Links");
echo button_link("add_armorial.php?id=$id_person", "Add Armorial Device");
echo '<input type="hidden" name="id" value="'.$person["id_person"].'">';
echo '<input type="hidden" name="form_name" value="edit_armorial">';

echo "</br>Please separate keywords with spaces";

echo "<table class='table table-condensed table-bordered'>";
if (isset($_POST["form_name"])
        && ($_POST["form_name"]=="edit_armorial")
        && isset($_POST["search_filters"])
        && is_string($_POST["search_filters"])) {
    $search_filters=$_POST["search_filters"];
} else {
    $search_filters="";
}
echo '<tr><td class="text-right">Search Filters:</td>'
     . '<td><input type="text" name="search_filters" value="'
     . $search_filters.'"></td></tr>';
echo '</table>';
echo '<input type="submit" value="Update Search Filters">';
echo '</form>';

// Build table with links etc in 2 parts: existing links in one table, possible new links in the next

// Query for existing links (if any)
$q_exist = "SELECT id_person_armorial as ipa, id_person as ip, Armorials.id_armorial as ia, "
        . "type_armorial as type, blazon_armorial as blazon, image_armorial as image, "
        . "fname_armorial as fname, ftype_armorial as ftype "
        . "FROM Persons_Armorials, Armorials "
        . "WHERE Persons_Armorials.id_armorial = Armorials.id_armorial "
        . "AND id_person = :id_person";
$data = ['id_person' => $id_person];
if (DEBUG) {
    echo "Query to list existing links is: $q_exist</br>";
}
$sth = $cxn->prepare($q_exist);
$sth->execute($data);
 

echo form_subtitle("Existing links:");
echo "<table class='table table-condensed table-bordered'>";
echo "<thead>"
        . "<td>Thumbnail</td>"
        . "<td>Filename</td>"
        . "<td>Blazon</td>"
        . "<td>Modifications</td>"
        . "</thead>";
while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
    extract($row);
    echo "<tr>";
    echo '<td>';
    // DISPLAY THUMBNAIL HERE: currently this code isn't working.


    display_image($image, $ftype, 100);

    echo '</td>';
    echo '<td>'.$fname.'</td>';
    echo '<td>'.$blazon.'</td>';
    echo '<td> Currently '.$type.':</br>';
    if ($type != 'device') {
        echo button_link("edit_person_armorial_link.php?ipa=$ipa&ip=$id_person&act=make_device", "Make device");
    }
    if ($type != 'badge') {
        echo button_link("edit_person_armorial_link.php?ipa=$ipa&ip=$id_person&act=make_badge", "Make badge");
    }
    if ($type != 'household') {
        echo button_link("edit_person_armorial_link.php?ipa=$ipa&ip=$id_person&act=make_household", "Make household");
    }
    echo button_link("edit_person_armorial_link.php?ipa=$ipa&ip=$id_person&act=delete", "Remove");

    echo '</td>';
    echo "</tr>";
}
echo "</table>";

// Query for adding new links based on search filters
// Note: if $search_filters = "", choose the 10 most recent images
$q_new = "SELECT id_armorial as ia, blazon_armorial as blazon, image_armorial as image, "
        . "fname_armorial as fname, fsize_armorial as fsize, ftype_armorial as ftype "
        . "FROM Armorials WHERE id_armorial NOT IN "
        . "(SELECT id_armorial FROM Persons_Armorials where id_person=:id_person) ";
if ($search_filters=="") {
    $q_new = $q_new . "ORDER BY timestamp_armorial DESC LIMIT 10";
} else {
    // need to explode the search filters
    $filters=  explode(" ", $search_filters);
    $q_new = $q_new . "AND ( ";
    foreach ($filters as $filter) {
        if (is_numeric($filter)) {
            $q_new=$q_new. " id_armorial = $filter OR ";
        } else {
            $q_new = $q_new . " blazon_armorial like '%$filter%' OR ";
            $q_new = $q_new . " fname_armorial like '%$filter%' OR ";
        }
    } 
    $q_new = $q_new . " ABS(TIMESTAMPDIFF(MINUTE,NOW(),timestamp_armorial)) < 15) ";
}
if (DEBUG) {
    echo "Query to list possible new links is: $q_new</br>";
}
$sth = $cxn->prepare($q_new);
$sth->execute($data);

echo form_subtitle("Potential new links based on seach filters:");
echo form_subsubtitle("(Also includes all files uploaded in last 15 minutes)");
echo "<details><summary class='btn btn-default' style='cursor: pointer'>Show/Hide Table</summary>";
echo "<table class='table table-condensed table-bordered'>";
echo "<thead>"
        . "<td>Thumbnail</td>"
        . "<td>Filename</td>"
        . "<td>Blazon</td>"
        . "<td>Modifications</td>"
        . "</thead>";
while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
    extract($row);
    echo "<tr>";
    echo '<td>';

    display_image($image, $ftype, 100);
    echo '</td>';
    echo '<td>'.$fname.'</td>';
    echo '<td>'.$blazon.'</td>';

    echo '<td>';
    echo button_link("edit_person_armorial_link.php?ip=$id_person&ia=$ia&act=add_device", "Make device");
    echo button_link("edit_person_armorial_link.php?ip=$id_person&ia=$ia&act=add_badge", "Make badge");
    echo button_link("edit_person_armorial_link.php?ip=$id_person&ia=$ia&act=add_household", "Make household");
    echo '</td>';
    echo "</tr>";

}
echo "</table>";
echo "</details>";

?>
