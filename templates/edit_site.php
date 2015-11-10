<?php
// Purpose: to display all data for event site we're about to edit,
// Privileges needed: permissions("Sites")>= 3

if (permissions("Sites")>= 3) {
   if ((isset($_GET['id'])) && (is_numeric($_GET['id'])) && (isset($_SESSION['id']))) {
       // We got here through the edit link on list_site.php
       $id_site = $_GET["id"];
   } elseif ((isset($_POST['id'])) && (is_numeric($_POST['id'])) && (isset($_SESSION['id']))) {
       // We got here from form submission
       // echo "Arrived as form submission";
       $id_site = $_POST['id'];
   }
} else {
    // We don't have sufficient permissions for this page.
    echo '<p class="error"> This page has been accessed in error.</p>';
    echo 'Please use your back arrow to return to the previous page.';
    exit_with_footer();
}

$cxn = open_db_browse();


if ($_SERVER['REQUEST_METHOD'] == 'POST'){
// Process form by updating the database
// TODO: Should only update if actual changes have been made.
//       How to test for that?
    // TODO: We need to filter these variables much more carefully
/*    $sca_name=$_POST["SCA_name"];
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
    // echo "<p>Query is " . $update . "<p>";
    $result=update_query($cxn, $update);
    if ($result !== 1) {echo "Error updating record: " . mysqli_error($cxn);}
*/
}

echo "
<div class='row'>
  <div class='col-md-8 col-md-offset-2'>";

$query = "SELECT * from Sites where id_site = $id_site;";
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute query");
if (mysqli_num_rows($result)==1) {
   $site= mysqli_fetch_assoc($result);
} else {
    exit_with_footer();
}

// Display form with all person's info.
echo '<form action="edit_site.php" method="post">';
echo "<h2>Editing Event Site Information</h2>";
//echo '<a href="list_site.php">Exit Edit Page</a>';
echo '<input type="hidden" name="id" value="'.$site["id_site"].'">';
echo "<table class='table table-condensed table-bordered'>";
//<thead><td class='text-right'>Column</td><td class='text-left'>Value</td></thead>";

/*****************************************************************************/
$varname="name_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $name_site=$_POST[$varname];
} else {
    $name_site=$site[$varname];
}
echo '<tr><td class="text-right">Name of Site:</td><td><input type="text" '
     . 'name="'.$varname.'" size="50" maxlength="256" value="'
     . $name_site.'"></td></tr>';
/*****************************************************************************/
$varname="url_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $url_site=$_POST[$varname];
} else {
    $url_site=$site[$varname];
}
echo '<tr><td class="text-right">URL of Site:</td><td><input type="text" '
     . 'name="'.$varname.'" size="50" maxlength="256" value="'
     . $url_site.'"></td></tr>';


echo "</table>";
echo '<input type="submit" value="Update Event Site Information">';
echo '</form>';

echo "</div><!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs
mysqli_close ($cxn); /* close the db connection */
?>
