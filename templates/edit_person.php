<?php
// Purpose: to display all data for person we're about to edit, 
// including edit, delete, and add more links for awards
// 
// Structure will consist of form (and form processing) for 
// personal information, above a list of awards etc.  Each award 
// will have edit/delete links, and there will be an add award at
//  the top of the list.
//
// Eventually the same structure will be added for authorization/warrants.
// ASSUMED: This page will only be reached by somebody who has the relevant
//          access privileges
// TODO: Check the page can only be accessed by people with proper privs.


if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
    // We got here through the edit link on person.php
    // echo "Arrived from person.php";
    $id_person = $_GET["id"];
} elseif ((isset($_POST['id'])) && (is_numeric($_POST['id']))) {
    // We got here from form submission
    // echo "Arrived as form submission";
    $id_person = $_POST['id'];
} else {
    echo '<p class="error"> This page has been accessed in error.</p>';
    require("../templates/footer.php");
    exit();
}

$cxn = open_db_browse();

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
// Process form by updating the database
    
}
// TODO: Test if person exists in database.  (If not, query returns 0 rows.)
$query = "SELECT * from Persons where id_person = $id_person;";
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute query");
if (mysqli_num_rows($result)==1) {
   $person=  mysqli_fetch_array($result);
}
$query = "SELECT id_group, "
        . "CONCAT(name_group,' (',name_kingdom,')') as Name_Group, "
        . "Groups.id_kingdom!=".HOST_KINGDOM_ID." as In_Kingdom "
        . "FROM Groups, Kingdoms "
        . "WHERE Groups.id_kingdom = Kingdoms.id_kingdom "
        . "Order By In_Kingdom, Name_Group;";
$groups = mysqli_query ($cxn, $query) or die ("Couldn't execute query");

// Display form with all person's info.
echo '<form action="edit_person.php" method="post">';
// SCA Name:
echo 'SCA Name:<input type="text" name="SCA_name" value="'.$person["name_person"].'"/><br>';
// SCA Group (dd all the possible groups to a selection box)
echo 'Current group id is '.$person["id_group"].'<br>';
echo 'SCA Group: <select name="id_group" >';
while ($row= mysqli_fetch_array($groups)) {
    echo '<option value="'.$row["id_group"].'"';
    if ($row["id_group"]==$person["id_group"]) echo ' selected';
    echo '>'.$row["Name_Group"].'</option>';
}
echo '</select>';

 echo '</form>';


// Display person's awards with edit & delete link for each award
 
?>
