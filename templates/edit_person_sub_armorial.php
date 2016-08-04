<?php
// This page links up the amorials and is available on to the Ruby Herald
// Set up search filters
echo "<div class='row'>
        <div class='col-md-8 col-md-offset-2'>";

echo '<form action="edit_person.php" method="post">';
echo form_title("Adding Armorial Links");
echo '<input type="hidden" name="id" value="'.$person["id_person"].'">';
echo '<input type="hidden" name="form_name" value="edit_armorial">';
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
        . "WHERE Persons_Armorials.id_armorial = Armorials.id_armorial";
if (DEBUG) {
    echo "Query to list existing links is: $q_exist</br>";
}
$curr_links = mysqli_query ($cxn, $q_exist)
        or die ("Couldn't execute query to find existing links");

echo "<table class='table table-condensed table-bordered'>";
echo "<thead>"
        . "<td>Thumbnail</td>"
        . "<td>Filename</td>"
        . "<td>Blazon</td>"
        . "<td>Modifications</td>"
        . "</thead>";
while ($row = mysqli_fetch_assoc($curr_links)) {
    extract($row);
    echo "<tr>";
    echo '<td>';
    // DISPLAY THUMBNAIL HERE: currently this code isn't working.



if ($image !== false) {
    switch ($ftype) {
        case "image/png"  : echo '<img src="data:image/png;base64,' . $image  . '" />';
            break;
        case "image/gif"  : echo '<img src="data:image/gif;base64,' . $image  . '" />';
            break;
        case "image/jpeg" : echo '<img src="data:image/jpeg;base64,' . $image  . '" />';
            break;
        case "image/jpg"  : echo '<img src="data:image/jpg;base64,' . $image  . '" />';
            break;
        default:
            echo "No image";
    }
  }

    echo '</td>';
    echo '<td>'.$fname.'</td>';
    echo '<td>'.$blazon.'</td>';
    echo '<td>';
    if ($type != 'device') {
        echo button_link("edit_person_armorial_link.php?ipa=$ipa&act='make_device", "Make device");
    }
    if ($type != 'badge') {
        echo button_link("edit_person_armorial_link.php?ipa=$ipa&act='make_badge", "Make badge");
    }
    if ($type != 'household') {
        echo button_link("edit_person_armorial_link.php?ipa=$ipa&act='make_household", "Make household");
    }
    echo button_link("edit_person_armorial_link.php?ipa=$ipa&act='delete", "Remove");

    echo '</td>';
    echo "</tr>";
}
echo "</table>";

// Query for adding new links based on search filters
// Note: if $search_filters = "", choose the 10 most recent images
$q_new = "SELECT id_armorial as ia, blazon_armorial as blazon, image_armorial as image, "
        . "fname_armorial as fname, fsize_armorial as fsize, ftype_armorial as ftype "
        . "FROM Armorials WHERE id_armorial NOT IN "
        . "(SELECT id_armorial FROM Persons_Armorials where id_person=$id_person) ";
if ($search_filters=="") {
    $q_new = $q_new . "ORDER BY timestamp_armorial ASC LIMIT 10";
} else {
    // need to explode the search filters
}
if (DEBUG) {
    echo "Query to list possible new links is: $q_new</br>";
}
$new_links = mysqli_query ($cxn, $q_new)
        or die ("Couldn't execute query to find existing links");

        echo "<table class='table table-condensed table-bordered'>";
        echo "<thead>"
                . "<td>Thumbnail</td>"
                . "<td>Filename</td>"
                . "<td>Blazon</td>"
                . "<td>Modifications</td>"
                . "</thead>";
        while ($row = mysqli_fetch_assoc($new_links)) {
            extract($row);
            echo "<tr>";
            echo '<td>';

        if ($image !== false) {
            switch ($ftype) {
                case "image/png"  : echo '<img src="data:image/png;base64,' . $image  . '" />';
                    break;
                case "image/gif"  : echo '<img src="data:image/gif;base64,' . $image  . '" />';
                    break;
                case "image/jpeg" : echo '<img src="data:image/jpeg;base64,' . $image  . '" />';
                    break;
                case "image/jpg"  : echo '<img src="data:image/jpg;base64,' . $image  . '" />';
                    break;
                default:
                    echo "No image";
            }
          }

            echo '</td>';
            echo '<td>'.$fname.'</td>';
            echo '<td>'.$blazon.'</td>';

          echo '<td>';
          echo "Link button goes here";
          /*  this section does not work right
            if ($type != 'device') {
                echo button_link("edit_person_armorial_link.php?ipa=$ipa&act='make_device", "Make device");
            }
            if ($type != 'badge') {
                echo button_link("edit_person_armorial_link.php?ipa=$ipa&act='make_badge", "Make badge");
            }
            if ($type != 'household') {
                echo button_link("edit_person_armorial_link.php?ipa=$ipa&act='make_household", "Make household");
            }
            echo button_link("edit_person_armorial_link.php?ipa=$ipa&act='delete", "Remove");
            */
            echo '</td>';
            echo "</tr>";

        }
        echo "</table>";
echo "</div> </div>"; // class=col-md-8, class=row,
?>
