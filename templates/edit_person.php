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
    echo "Arrived from person.php";
    $id_person = $_GET["id"];
} elseif ((isset($_POST['id'])) && (is_numeric($_POST['id']))) {
    // We got here from form submission
    echo "Arrived as form submission";
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
// Display form with all person's info.

// Display person's awards with edit & delete link for each award
?>