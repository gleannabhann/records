<?php

/* Add Events to the database
 * 
 */
if (DEBUG){
    var_dump($_SESSION);
}

if (permissions("Herald")>=  3) {
// // If we got here from Post: 
//    - add the new site and include a message
//    - reset the form?
    $cxn = open_db_browse();
    if ($_SERVER['REQUEST_METHOD'] ==  'POST') {
    // Build the update query
       $query_head = "INSERT INTO Events (name_event" ;
       $query_tail = " VALUES (";
       
       $name_event =   sanitize_mysql($_POST["name_event"]);
       $query_tail = $query_tail."'$name_event'";
       
       $varname = "id_group";
       if (isset($_POST[$varname]) && !empty($_POST[$varname]) 
               && is_numeric($_POST[$varname])) {
            $id_group = $_POST[$varname];
            $query_head = $query_head.",$varname";
            $query_tail = $query_tail.",$id_group";
       }

       $varname = "id_site";
       if (isset($_POST[$varname])
               && is_numeric($_POST[$varname])
               && ($_POST[$varname] > 0)) {
            $id_site = $_POST[$varname];
            $query_head = $query_head.",$varname";
            $query_tail = $query_tail.",$id_site";
       }
       
       $varname = "date_event_start";
       if (isset($_POST[$varname]) && !empty($_POST[$varname]) 
               && is_string($_POST[$varname])) {
            $date_event_start = $_POST[$varname];
            $query_head = $query_head.",$varname";
            $query_tail = $query_tail.",'$date_event_start'";
       }
       $varname = "date_event_stop";
       if (isset($_POST[$varname]) && !empty($_POST[$varname]) 
               && is_string($_POST[$varname])) {
            $date_event_stop = $_POST[$varname];
            $query_head = $query_head.",$varname";
            $query_tail = $query_tail.",'$date_event_stop'";
       }

       $query_head=$query_head.") ";
       $query_tail=$query_tail.");";
       $query = $query_head.$query_tail;
       if (DEBUG) {
           echo "<p> Update query is:<br> $query<br>";
       }
       $result=update_query($cxn, $query);
       if ($result !== 1) {
           echo "Error updating record: " . mysqli_error($cxn);
       } else {
           echo "Successfully added "
           . stripslashes($name_event). " to the list of known events.<br>";
           echo 'Continue adding new events below:';
       }    }

    // Queries needed to build the form
    $query = "SELECT Groups.id_group, name_group, name_kingdom, "
            . "Groups.id_kingdom !=".HOST_KINGDOM_ID." as In_Kingdom "
            . "FROM Groups, Kingdoms "
            . "WHERE Groups.id_kingdom = Kingdoms.id_kingdom "
            . "ORDER BY In_Kingdom, name_group";
    if (DEBUG) {
        echo "<p>Groups query is:<br>$query<br>";
    }
    $groups = mysqli_query($cxn, $query) or die ("Couldn't execute groups query");

    $query = "SELECT id_site, name_site "
            . "FROM Sites "
            . "WHERE active_site=1 "
            . "ORDER BY name_site";
    if (DEBUG) {
        echo "<p>Sites query is:<br>$query<br>";
    }
    $sites = mysqli_query($cxn, $query) or die ("Couldn't execute sites query");
    mysqli_close($cxn); /* close the db connection */
} else {
    // We don't have sufficient permissions for this page.
    echo '<p class = "error"> This page has been accessed in error.</p>';
    echo 'Please use your back arrow to return to the previous page.';
    exit_with_footer();
}

?>

<div class='row'><div class='col-md-8 col-md-offset-2'>
<form action="add_event.php" method="post">
  <?php echo form_title("Adding a New Event"); ?>
  <table class='table table-condensed table-bordered'>
      <tr>
          <td class="text-right">Name of Event<br>(Required)</td>
          <td><input type="text" name="name_event" size="50" maxlength="128" required>
          </td>
      </tr>
      <tr>
          <td class="text-right">Date Event Starts</td>
          <td><input type="date" class="date" name="date_event_start">
              <br>(format if no datepicker: yyyy-mm-dd)</td>
      </tr>
      <tr>
          <td class="text-right">Date Event Ends</td>
          <td><input type="date" class="date" name="date_event_stop">
              <br>(format if no datepicker: yyyy-mm-dd)</td>
      </tr>
      <tr>
          <td class="text-right">Hosting Group</td>
          <td>
              <select name="id_group">
                  <option value="-1"> --- </option>
                  <?php
                  while ($row= mysqli_fetch_array($groups)) {
                    echo '<option value="'.$row["id_group"].'"';
                    echo '>'.$row["name_group"]
                            .'('.$row["name_kingdom"].')'.'</option>';
                  }
                  ?>
              </select>              
          </td>
      </tr>
      <tr>
          <td class="text-right">Site</td>
          <td>
              <select name="id_site">
                  <option value="-1"> --- </option>
                   <?php
                  while ($row= mysqli_fetch_array($sites)) {
                    echo '<option value="'.$row["id_site"].'"';
                    echo '>'.$row["name_site"].'</option>';
                  }
                  ?>                
              </select>
          </td>
      </tr>
  </table>
  <input type="submit" value="Add Event Information">
</form>  
</div><!-- ./col-md-8 --></div><!-- ./row -->