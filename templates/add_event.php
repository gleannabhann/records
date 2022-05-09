<?php

/* Add Events to the database
 * 
 */
if (DEBUG){
  // create array of message and vars
  $arr = ['message' => 'session', 'vars' => $_SESSION];
  // convert array to json
  $message = json_encode($arr);
  // post the json to the debug log
  error_log($message, 3, DEBUG_DEST);
}

if (permissions("Herald")>=  3) {
// // If we got here from Post: 
//    - add the new site and include a message
//    - reset the form?
  /* header has opened the db TODO verify this */

    if ($_SERVER['REQUEST_METHOD'] ==  'POST') {
      // Build the update query
      $data = [];
      $query_head = "INSERT INTO Events (name_event" ;
      $query_tail = " VALUES (";
      $query_tail = $query_tail.":name_event";
      $name_event = $_POST['name_event']; // this gets used later
      $data[':name_event'] = $name_event;

      $varname = "id_group";
      if (isset($_POST[$varname]) && !empty($_POST[$varname]) 
             && is_numeric($_POST[$varname])) {
          $id_group = $_POST[$varname];
      } else {$id_group=-1;}
      $query_head = $query_head.",$varname";
      $query_tail = $query_tail.",:id_group";
      $data[':id_group'] = $id_group;      
      
      $varname = "id_site";
      if (isset($_POST[$varname])
              && is_numeric($_POST[$varname])
              && ($_POST[$varname] > 0)) {
           $id_site = $_POST[$varname];
      } else { $id_site=-1;}
      $query_head = $query_head.",$varname";
      $query_tail = $query_tail.",:id_site";
      $data[':id_site'] = $id_site;

      $varname = "date_event_start";
      if (isset($_POST[$varname]) && !empty($_POST[$varname]) 
              && is_string($_POST[$varname])) {
           $date_event_start = $_POST[$varname];
           $query_head = $query_head.",$varname";
           $query_tail = $query_tail.",:date_event_start";
           $data[':date_event_start'] = $date_event_start;
      }
      $varname = "date_event_stop";
      if (isset($_POST[$varname]) && !empty($_POST[$varname]) 
              && is_string($_POST[$varname])) {
           $date_event_stop = $_POST[$varname];
           $query_head = $query_head.",$varname";
           $query_tail = $query_tail.",:date_event_stop";
           $data[':date_event_stop'] = $date_event_stop;
      }

      $query_head=$query_head.") ";
      $query_tail=$query_tail.");";
      $query = $query_head.$query_tail;
      if (DEBUG) {
        $msg = 'add_event.php insert query is';
        $vars = ['query' => $query, 'data' => $data];
        $arr = ['message' => $msg, 'vars' => $vars];
        $message = json_encode($arr);
        error_log($message, 3, DEBUG_DEST);
      }
      try {
        $result=update_query($cxn, $query, $data);
        echo "<div class='row'><div class='col-sm-12 col-md-8 col-md-offset-2'>";
        $success = "Successfully added <strong>"
          . stripslashes($name_event). "</strong> to the list of known events.<br>"
          . '<br>Continue adding new events below:';
        bs_alert($success, 'success');
        echo "</div></div>"
      } catch (PDOException $e) {
          $friendly_message = "Could not add the Event.";
          if (DEBUG) {
            $vars = ['exc_msg' => $e->getMessage(), 'exc_code' => $e->getCode()];
            $arr = ['message' => $friendly_message, 'exception' => $vars];
            $message = json_encode($arr);
            error_log($message, 3, DEBUG_DEST);
          }
          bs_alert($friendly_message, 'danger');
      }
    }    


// Queries needed to build the form
    $k_query = "SELECT host_kingdom_id FROM Appdata WHERE app_id=1";
    try {
      $sth = $cxn->query($k_query);
    } catch (PDOException $e) {
      if (DEBUG) {
        $msg = "Couldn't fetch the kingdom id";
        $vars = ['exc_msg' => $e->getMessage(), 'exc_code' => $e->getCode()];
        $arr = json_encode($arr);
        error_log($message, 3, DEBUG_DEST);
      }
    }
    $k_id = $sth->fetch();
    $q_groups = "SELECT Groups.id_group, name_group, name_kingdom, "
            . "Groups.id_kingdom =:k_id as In_Kingdom "
            . "FROM Groups, Kingdoms "
            . "WHERE Groups.id_kingdom = Kingdoms.id_kingdom "
            . "ORDER BY In_Kingdom DESC, name_group";
    $d_groups = [':k_id' => $k_id['host_kingdom_id']];
    $q_sites = "SELECT id_site, name_site "
            . "FROM Sites "
            . "WHERE active_site=1 "
            . "ORDER BY name_site";
    $q_count = 0;
    try {
      $sth_groups = $cxn->prepare($q_groups);
      $sth_groups->execute($d_groups);
      $q_count++;
      $sth_sites = $cxn->query($q_sites);
      $q_count++;
    } catch (PDOException $e) {
      $f_message = "I couldn't fetch the information I need to build the form.";
      if (DEBUG) {
        $q_part = NULL;
        if ($q_count == 0) { 
          $q_part = "groups"; 
          $var = ['query' => $q_groups, 'data' => $d_groups];
        }
        if ($q_count == 1) { 
          $q_part = "sites"; 
          $var = ['query' => $q_sites];
        }
        $err_msg = "Problem fetching $q_part. ";
        $vars = ['exc_msg' => $e->getMessage(), 'exc_code' => $e->getCode, 'query_details' => $var];
        $arr = ['message' => $err_msg, 'vars' => $vars];
        $message = json_encode($arr);
        error_log($message, 3, DEBUG_DEST);
      }
      bs_alert($message, 'warning');
      exit_with_footer();
    }
} else {
    // We don't have sufficient permissions for this page.
    echo '<p class = "error"> This page has been accessed in error.</p>';
    echo 'Please use your back arrow to return to the previous page.';
    exit_with_footer();
}

?>

<div class='row'><div class='col-md-8 col-md-offset-2'>
<form action="add_event.php" method="post">
  <?php echo form_title("Adding a New Event"); 
    echo button_link("list_events.php", "List of Events")."</p>";
  ?>
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
                  while ($row= $sth_groups->fetch()) {
                    echo '<option value="'.$row["id_group"].'"';
                    echo '>'.$row["name_group"]
                            .' ('.$row["name_kingdom"].')'.'</option>';
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
                  while ($row= $sth_sites->fetch()) {
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
