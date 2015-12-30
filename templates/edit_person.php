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


// Checks for privs is conducted by code in config.php.

if ((!permissions("Herald")>=3) && (!permissions("Marshal")>=3)) {
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}

if ((isset($_GET['id'])) && (is_numeric($_GET['id'])) && (isset($_SESSION['id']))) {
    // We got here through the edit link on person.php
    // echo "Arrived from person.php";
    $id_person = $_GET["id"];
} elseif ((isset($_POST['id'])) && (is_numeric($_POST['id'])) && (isset($_SESSION['id']))) {
    // We got here from form submission
    // echo "Arrived as form submission";
    $id_person = $_POST['id'];
} else  {
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}

$cxn = open_db_browse();

// Edit the personal information like name, mundane info, etc.
if ((permissions("Marshal")>=3) || (permissions("Herald")>=3)){
    include 'edit_person_sub_personal_info.php';
}
//echo "Permissions for herald is ".permissions("Herald")."<br>";
//echo "<p>".var_dump($_SESSION)."<p>";

// Edit authorization and warrant stuffs for person
if (permissions("Marshal")>= 3) {
    
        form_subtitle("Now heading to Marshal territory");
    
    include 'edit_person_sub_authorizations.php';
}
// Edit awards for person
if ((permissions("Herald")>= 3) && (permissions("Obsidian")>=3)){
   include 'edit_person_sub_awards.php';
}


mysqli_close ($cxn); /* close the db connection */
?>
