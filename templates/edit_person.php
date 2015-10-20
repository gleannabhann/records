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
    $sca_name=$_POST["SCA_name"];
    $mundane_name=$_POST["mundane_name"];
    $email = $_POST["email"];
    $mem_num = $_POST["mem_num"];
    $mem_exp = $_POST["mem_exp"];
    $id_group = $_POST["id_group"];
  // TODO: Need to worry about expiry date: for browsers not using 
  // the date type in the form, dates have to be entered as yyyy-mm-dd
    $update = "UPDATE Persons SET ";
    if (!empty($sca_name)){ $update=$update . "name_person='" . $sca_name . "'" ;}
if (!empty($mundane_name)) {$update=$update . ", name_mundane_person='" . $mundane_name ."' ";}
    if (!empty($email)) {$update=$update . ", email_person='" . $email."' ";}
    if (!empty($mem_num)) {$update=$update . ", membership_person=" . $mem_num." ";}
    if (!empty($mem_exp)) {$update=$update . ", membership_expire_person='" . $mem_exp."' ";}
    if (!empty($id_group)) {$update=$update . ", id_group = " . $id_group;}
    $update=$update. " WHERE id_person=" .$id_person;
   echo "<p>Query is " . $update . "<p>";
    if  (mysqli_query($cxn, $update)) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . mysqli_error($cxn);
    }
    
}
// TODO: Test if person exists in database.  (If not, query returns 0 rows.)
echo "<h2>Editing Personal Information</h2>";

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
echo '<input type="hidden" name="id" value="'.$person["id_person"].'">';
// SCA Name:
if (isset($_POST["SCA_name"])&& is_string($_POST["SCA_name"])) {
    $sca_name=$_POST["SCA_name"];
} else {
    $sca_name=$person["name_person"];
}
echo 'SCA Name: <input type="text" name="SCA_name" value="'
     . $sca_name.'"><br>';
// Mundane Name
if (isset($_POST["mundane_name"])&& is_string($_POST["mundane_name"])) {
    $mundane_name=$_POST["mundane_name"];
} else {
    $mundane_name=$person["name_mundane_person"];
}
echo 'Mundane Name: <input type="text" name="mundane_name" value="'
     . $mundane_name.'"><br>';
// SCA email address
if (isset($_POST["email"]) && is_string($_POST["email"])) {
    $email = $_POST["email"];
} else {
    $email = $person["email_person"];
}
echo 'Email address: <input type="email" name="email" value =" '
    . $email . '"><br>';
// SCA Membership Number
if (isset($_POST["mem_num"]) && is_numeric($_POST["mem_num"])) {
    $mem_num = $_POST["mem_num"];
} else {
    $mem_num = $person["membership_person"];
}
echo 'SCA Membership Number: <input type="number" name="mem_num" value="'
    . $mem_num .'">';
// SCA Membership Expiry Date
if (isset($_POST["mem_exp"]) && is_string($_POST["mem_exp"])) {
    $mem_exp = $_POST["mem_exp"];
} else {
    $mem_exp = $person["membership_expire_person"];
}
echo '&nbsp expires <input type="date" class="date" name="mem_exp" value="'
    . $mem_exp . '"><br>';
// SCA Group (add all the possible groups to a selection box, current group is selected)
if (isset($_POST["id_group"]) && is_numeric($_POST["id_group"])) {
    $id_group = $_POST["id_group"];
} else {
    $id_group = $person["id_group"];
}
echo 'SCA Group: <select name="id_group" >';
while ($row= mysqli_fetch_array($groups)) {
    echo '<option value="'.$row["id_group"].'"';
    if ($row["id_group"]==$id_group) echo ' selected';
    echo '>'.$row["Name_Group"].'</option>';
}
echo '</select><p>';
echo '<input type="submit" value="Update Personal Information">';
echo '</form>';

echo "<p>";

echo "<h2>Editing awards</h2>";
echo "<a href='./add_person_award.php?id=$id_person'>Add a new award</a>";
echo "
<div class='row'>
  <div class='col-md-8 col-md-offset-2'>";
echo "<table class='table table-condensed table-bordered'>
<thead><td class='text-left'><strong>Award</strong></td>
<td class='text-left'><strong>Date</strong></td><td>Edit</td><td>Delete</td></thead>";

// Display person's awards with edit & delete link for each award
 $query = "SELECT  id_person_award, name_award, date_award,name_kingdom from Persons, Persons_Awards, Awards, Kingdoms
   WHERE Persons.id_person = Persons_Awards.id_person
         and Persons_Awards.id_award = Awards.id_award
         and Awards.id_kingdom = Kingdoms.id_kingdom
         and Persons.id_person = $id_person order by date_award";
 // echo "Query to list awards is: ".$query."<br>";
$awards = mysqli_query ($cxn, $query) or die ("Couldn't execute query");
while ($row = mysqli_fetch_assoc($awards))
  {extract($row);
// echo "<tr><td class='text-left'>$name_award - $name_kingdom</td><td class='text-left'>$date_award</tr></td>";
  echo "<tr><td class='text-left'>$name_award</td><td class='text-left'>$date_award</td>";
  echo "<td><a href='./edit_person_award.php?idpa=$id_person_award'>Edit</a></td>";
  echo "<td><a href='./delete_person_award.php?idpa=$id_person_award'>Delete</a></td>";
  echo "</tr>";
};
echo "</table>";
echo "</div><!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs
?>
