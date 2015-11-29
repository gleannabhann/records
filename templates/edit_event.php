<?php
if (permissions("Herald")>=  3) {
    if ((isset($_GET['id'])) && (is_numeric($_GET['id'])) && (isset($_SESSION['id']))) {
        // We got here through the edit link on person.php
        // echo "Arrived from person.php";
        $id_event = $_GET["id"];
    } elseif ((isset($_POST['id'])) && (is_numeric($_POST['id'])) && (isset($_SESSION['id']))) {
        // We got here from form submission
        // echo "Arrived as form submission";
        $id_event = $_POST['id'];
    } else  {
        echo '<p class="error"> This page has been accessed in error.</p>';
        exit_with_footer();
    }    
    $cxn = open_db_browse();
    // Let's find out what we know from the database
    $query = " SELECT name_event, date_event_start, date_event_stop, id_site, id_group "
            . "FROM Events "
            . "WHERE id_event=$id_event;";
    $result = mysqli_query ($cxn, $query) or die ("Couldn't execute query to find site info");
    if (DEBUG) { echo "Event info query is: $query";}
    if (mysqli_num_rows($result)==1) {
       $event= mysqli_fetch_assoc($result);
    } else {
        exit_with_footer();
    }
    echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
    // Build the form, populating fields based on the post variable or database variable
    echo form_title("Editing Event Information")."\n";
    echo '<form action="edit_person.event" method="post">';
    echo '<input type="hidden" name="id" value="'.$id_event.'"'.">\n";

    /*****************************************************************************/
    $varname="name_event";
    if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
        $name_event=$_POST[$varname];
    } else {
        $name_event=$event[$varname];
    }
    echo '<div class="form-group"><label for='.$varname.'>Name of Event:</label><input type="text" '
         . 'name="'.$varname.'" maxlength="128" value="'
         . $name_event.'" required>'
         . '<br/>This field is required</div>'."\n";
    
    /*****************************************************************************/
    $varname="date_event_start";
    if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
        $date_event_start=$_POST[$varname];
    } else {
        $date_event_start=$event[$varname];
    }
    echo "<div class='form-group'><label for='$varname'>Event Starts:</label>"
         . "<input type='date' class='date' name='$varname'  value='$date_event_start'></div>";

    /*****************************************************************************/
    $varname="date_event_stop";
    if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
        $date_event_stop=$_POST[$varname];
    } else {
        $date_event_stop=$event[$varname];
    }
    echo "<div class='form-group'><label for='$varname'>Event Ends:</label>"
         . "<input type='date' class='date' name='$varname'  value='$date_event_stop'></div>";
         

    echo '<input type="submit" value="Update Event Information">';
    //echo '<button type="reset" value="Reset">Reset</button>';
    echo '</form>';
    echo "</div><!-- ./col-md-8 --></div><!-- ./row -->";
    // Process the form: now that the variables are all populated,
    // let's go ahead and update the database if the Update button was pressed.
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        // First, update local variables
        // Build Update Query
        
    }
    
    mysqli_close ($cxn); /* close the db connection */
} else {
    // We don't have sufficient permissions for this page.
    echo '<p class = "error"> This page has been accessed in error.</p>';
    echo 'Please use your back arrow to return to the previous page.';
    exit_with_footer();
}

?>