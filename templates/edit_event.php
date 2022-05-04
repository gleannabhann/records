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
        bs_alert('This page has been accessed in error.', 'warning');
        exit_with_footer();
    }
    /* header.php and header_main.php establish the database connection */

    // Let's find out what we know from the database
    $query = " SELECT name_event, date_event_start, date_event_stop, id_site, id_group "
            . "FROM Events "
            . "WHERE id_event=:id_event;";
    $data = [':id_event' => $id_event];
    $sth = $cxn->prepare($query);
    try {
      $sth->execute($data);
    } catch (PDOException $e) {
      $error = "Unable to locate event in database.";
      if (DEBUG) {
        $message = $e->getMessage();
        $code = (int)$e->getCode();
        $error = $error . " $message / $code ";
      } 
      bs_alert($error, 'danger');
      exit_with_footer();
    }

    if ($sth->rowCount()==1) {
      // get the event information
      $event= $sth->fetch();
    } else {
        exit_with_footer();
    }
    $query = "SELECT * FROM Appdata WHERE app_id=1";
    try {
      $sth = $cxn->prepare($query);
      $sth->execute();
      $appdata = $sth->fetch();
    } catch (PDOException $e) {
      $error ="Unable to find information about the Host Kingdom. ";
      if (DEBUG) {
        $message = $e->getMessage();
        $code = (int)$e->getCode();
        $error = $error . "$message / $code";
      }
      bs_alert($error, 'danger');
      exit_with_footer();
    }

    // get groups array    
    $query= "SELECT id_group, name_group, name_kingdom,"
            . "Groups.id_kingdom =:k_id as In_Kingdom "
            . "FROM Groups, Kingdoms "
            . "WHERE id_group > -1 "
            . "AND Groups.id_kingdom = Kingdoms.id_kingdom "
            . "ORDER BY In_Kingdom DESC, name_group;";
    $k_id = $appdata['host_kingdom_id'];
    $data = [':k_id' => $k_id];
    $sth_groups = $cxn->prepare($query);
    $sth_groups->execute($data);

    // get sites array
    $query= "SELECT id_site, name_site "
            . "FROM Sites WHERE id_site > -1 "
            . "AND active_site=1 "
            . "ORDER BY name_site;";
    $sth_query = $cxn->prepare($query);
    $sth_query->execute();
    
    if (DEBUG) { echo "Sites info query is: $query</br>";}
    
    echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
    // Build the form, populating fields based on the post variable or database variable
    echo form_title("Editing Event Information")."\n";
    echo button_link("event.php?id=$id_event", "Return to Event Overview");
    echo button_link("list_events.php", "Return to List of Events");
    echo '<form action="edit_event.php" method="post">';
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

    /*****************************************************************************/
    $varname="id_group";
    if (isset($_POST[$varname]) && is_numeric($_POST[$varname])) {
        $id_group=$_POST[$varname];
    } else {
        $id_group=$event[$varname];
    }
    echo "<div class='form-group'><label for='$varname'>Hosted by:</label>"
            . "<select name='id_group'>";
    echo "<option value='-1'> Unknown</option>";
    while ($row= $sth_groups->fetch()) {
        echo '<option value="'.$row["id_group"].'"';
        if ($row["id_group"]==$id_group) {
            echo ' selected';
        }
        echo ">".$row["name_group"];
        if (!$row["In_Kingdom"]) {
            echo " (".$row["name_kingdom"].")";
        }

        echo " </option>";
    }
    echo "</select>";

    /*****************************************************************************/
    $varname="id_site";
    if (isset($_POST[$varname]) && is_numeric($_POST[$varname])) {
        $id_site=$_POST[$varname];
    } else {
        $id_site=$event[$varname];
    }
    echo "<div class='form-group'><label for='$varname'>Hosted by:</label>"
            . "<select name='id_site'>";
    echo "<option value='-1'> Unknown</option>";
    while ($row= $sth_sites->fetch()) {
        echo '<option value="'.$row["id_site"].'"';
        if ($row["id_site"]==$id_site) {
            echo ' selected';
        }
        echo ">".$row["name_site"];
        echo " </option>";
    }
    echo "</select>";
    echo '<input type="submit" value="Update Event Information">';
    //echo '<button type="reset" value="Reset">Reset</button>';
    echo '</form>';
    echo "</div><!-- ./col-md-8 --></div><!-- ./row -->";
    // Process the form: now that the variables are all populated,
    // let's go ahead and update the database if the Update button was pressed.

    // init our params array
    $data = [];
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        // First, update local variables
        // Build Update Query
        $update = "UPDATE Events SET name_event=:name_event";
        $data[':name_event'] = $name_event;
        if ($date_event_start != $event["date_event_start"]) {
          $update = $update . ", date_event_start = :date_event_start";
          $data[':date_event_start'] = $date_event_start;
        }
        if ($date_event_stop != $event["date_event_stop"]) {
          $update = $update . ", date_event_stop = :date_event_stop";
          $data[':date_event_stop'] = $date_event_stop;
        }
        if ($id_group != $event["id_group"]) {
          $update = $update . ", id_group=:id_group";
          $data[':id_group'] = $id_group;
        }
        if ($id_site != $event["id_site"]) {
          $update = $update . ", id_site=:id_site";
          $data[':id_site'] = $id_site;
        }
        $update = $update." WHERE id_event=:id_event";
        $data[':id_event'] = $id_event;
        if (DEBUG){
          echo "Update Query is:<p>$update";
          echo "<p>Vars are<br>";
          print_r($data);
          echo "</p>";
        }
        try {
          $result=update_query($cxn, $update, $data);
        } catch (PDOException $e) {
          $error = "Error updating record. Please contact the system Administrator.";
          if (DEBUG) {
            $message = $e->getMessage();
            $code = (int)$e->getCode();
            $error = $error . " $message / $code ";
          }
          bs_alert($error, 'danger');
          exit_with_footer(); 
        }
        echo "<div class='alert alert-success center-block'>";
          echo "<p class='text-center'>Record Updated!</p>";
          echo "</div>";
      }

    /* footer.php will close the db connection for us */
} else {
    // We don't have sufficient permissions for this page.
    echo "<div class='alert alert-warning center-block'>";
    echo '<p class = "error"> This page has been accessed in error.</p>';
    echo '<p>Please use your back arrow to return to the previous page.</p>';
    echo '</div>';
    exit_with_footer();
}

?>
