<?php

if (permissions("Herald")<3){
    // We don't have permission to add awards so let's just exit now.
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}

if (($_SERVER['REQUEST_METHOD'] == 'POST') && (permissions("Herald")>=3)){
  // We have a form submission.  
  // Note: we allow for addition of multiple awards which is why the blank
  //       award form will reappear at the bottom of the page

  // init data array
  $data = [];
  $query_head = "INSERT INTO Awards(name_award";
  $query_tail = " VALUES('".  str_replace("'", "&#039;", $_POST["name_award"])."'";    
  if (isset($_POST["id_group"])
  && (!empty($_POST["id_group"]))
  && (is_numeric($_POST["id_group"]))){
      $query_head=$query_head.",id_group";
      $query_tail=$query_tail.", :id_group";
      $data[':id_group'] = $_POST["id_group"];
  }
  if (isset($_POST["id_kingdom"])
  && (!empty($_POST["id_kingdom"]))
  && (is_numeric($_POST["id_kingdom"]))){
      $query_head=$query_head.",id_kingdom";
      $query_tail=$query_tail.", :id_kingdom";
      $data[':id_kingdom'] = $_POST["id_kingdom"];
  }
  if (isset($_POST["id_rank"])
  && (!empty($_POST["id_rank"]))
  && (is_numeric($_POST["id_rank"]))){
      $query_head=$query_head.",id_rank";
      $query_tail=$query_tail.", :id_rank";
      $data[':id_rank'] = $_POST['id_rank'];
  }
  
  $query = $query_head.") ".$query_tail.");";
  if (DEBUG) { echo "<p>Insert Query is:<br>$query<br/>Vars:<br/>". json_encode($data). "</p>"; }
  // create a wrapper for our results
  echo "<div class='row'><div class='col-sm-12 col-md-8 col-md-offset-2'>";
  try {
    $result=update_query($cxn, $query, $data);
    $message = "<strong>Success!</strong> ".$_POST["name_award"]." has been added to the database<br/>";
    $message .= button_link("awards.php", "Return to awards page");
    $message .= "<br/>or continue adding new awards below.";
    bs_alert($message, 'success');
  } catch (PDOException $e) {
    $error = "<strong>Warning!</strong> Could not add ". $_POST['name_award'];
    if (DEBUG) { $error = add_pdo_exception($error, $e);   }
    bs_alert($error, 'danger');
  } 
  echo "</div></div>"; // close the results wrapper  
}

// Set up the data base queries to populate the form

// TODO work out single query that grabs the host_kingdom_id
// and then uses it to do the labeling in the 2nd query here
// in the mean time, we'll take the long-hand route
$k_query = "SELECT host_kingdom_id FROM Appdata WHERE app_id=1";
try {
  $sth = $cxn->query($k_query);
} catch (PDOException $e) {
  if (DEBUG) {
    $error = "Couldn't get the kingdom ID from the db. ";
    $error = add_pdo_exception($error, $e);
    bs_alert($error, 'danger');
  }
}

$k_id = $sth->fetch();

$q_groups = "SELECT id_group, "
        . "CONCAT(name_group,' (',name_kingdom,')') as name_group, "
        . "Groups.id_kingdom!=:k_id as In_Kingdom "
        . "FROM Groups, Kingdoms "
        . "WHERE Groups.id_kingdom = Kingdoms.id_kingdom "
        . "AND id_group >= 0 "
        . "Order By In_Kingdom, name_group;";
$d_groups = [':k_id' => $k_id['host_kingdom_id']];
//echo $query;
$q_kingdoms = "SELECT id_kingdom, name_kingdom FROM Kingdoms;";
$q_ranks = "SELECT id_rank, name_rank FROM Ranks;";
$q_count = 0;
try {
  $sth_groups = $cxn->prepare($q_groups);
  $sth_groups->execute($d_groups);
  $q_count++;
  $sth_kingdoms = $cxn->query($q_kingdoms);
  $q_count++;
  $sth_ranks = $cxn->query($q_ranks);
  $q_count++;
} catch (PDOException $e) {
  if (DEBUG) {
    $q_part = NULL;
    if ($q_count == 0) { $q_part = "groups"; }
    if ($q_count == 1) { $q_part = "kingdoms"; }
    if ($q_count == 2) { $q_part = "ranks"; }
    $error = "Problem fetching $q_part. ";
    $error = add_pdo_exception($error, $e);
    bs_alert($error, 'danger');
  }
}

 
?>

<div class='row'><div class='col-md-8 col-md-offset-2'>
<form action="add_award.php" method="post">
  <?php echo form_title("Adding a New Award"); ?>
  <table class='table table-condensed table-bordered'>
      <tr>
          <td class="text-right">Award Name<br>(Required)</td>
          <td><input type="text" name="name_award" size="50" maxlength="128" required>
          </td>
      </tr>
      <tr>
          <td class="text-right">Group of Award (if any)</td>
          <td><select name="id_group" ><option value="-1"></option>
          <?php 
          while ($row= $sth_groups->fetch()) {
                echo '<option value="'.$row["id_group"].'">'.$row["name_group"].'</option>';
          } ?>
          </td>
      </tr>
      <tr>
          <td class="text-right">Kingdom of Award<br>Required</td>
          <td><select name="id_kingdom" required></option>
          <?php 
          while ($row= $sth_kingdoms->fetch()) {
              echo '<option value="'.$row["id_kingdom"].'"';
              if ($row["id_kingdom"]==HOST_KINGDOM_ID) echo ' selected';
              echo '>'.$row["name_kingdom"].'</option>';
          } ?>
          </td>
      </tr>
      <tr>
          <td class="text-right">Rank of Award</td>
          <td><select name="id_rank" ></option>
          <?php 
          while ($row= $sth_ranks->fetch()) {
                echo '<option value="'.$row["id_rank"].'">'.$row["name_rank"].'</option>';
          } ?>
          </td>
      </tr>
  </table>
  <input type="submit" value="Add Award Information">
</form>
    </div></div>
<?/* the footer will close the db connection */?>
