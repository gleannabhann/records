<?php

if (permissions("Herald")<3){
    // We don't have permission to add awards so let's just exit now.
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}

$cxn = open_db_browse();

if (($_SERVER['REQUEST_METHOD'] == 'POST') && (permissions("Herald")>=3)){
    // We have a form submission.  
    // Note: we allow for addition of multiple awards which is why the blank
    //       award form will reappear at the bottom of the page
    
    $query_head = "INSERT INTO Awards(name_award";
    $query_tail = " VALUES('".  str_replace("'", "&#039;", $_POST["name_award"])."'";    
    if (isset($_POST["id_group"])
    && (!empty($_POST["id_group"]))
    && (is_numeric($_POST["id_group"]))){
        $query_head=$query_head.",id_group";
        $query_tail=$query_tail.",".$_POST["id_group"];
    }
    if (isset($_POST["id_kingdom"])
    && (!empty($_POST["id_kingdom"]))
    && (is_numeric($_POST["id_kingdom"]))){
        $query_head=$query_head.",id_kingdom";
        $query_tail=$query_tail.",".$_POST["id_kingdom"];
    }
    if (isset($_POST["id_rank"])
    && (!empty($_POST["id_rank"]))
    && (is_numeric($_POST["id_rank"]))){
        $query_head=$query_head.",id_rank";
        $query_tail=$query_tail.",".$_POST["id_rank"];
    }

    $query = $query_head.") ".$query_tail.");";
    if (DEBUG) { echo "Insert Query is:<br>$query<p>"; }
    $result=update_query($cxn, $query);
    if ($result !== 1) {
        echo "Error updating record: " . mysqli_error($cxn);
    } else {
        echo "Successfully added ".$_POST["name_award"]." to the database<p>";
        echo button_link("awards.php", "Return to awards page");
        echo "or continue adding new awards below<p>";
    }
}

// Set up the data base queries to populate the form
$query = "SELECT id_group, "
        . "CONCAT(name_group,' (',name_kingdom,')') as name_group, "
        . "Groups.id_kingdom!=".HOST_KINGDOM_ID." as In_Kingdom "
        . "FROM Groups, Kingdoms "
        . "WHERE Groups.id_kingdom = Kingdoms.id_kingdom "
        . "AND id_group >= 0 "
        . "Order By In_Kingdom, name_group;";
//echo $query;
$groups = mysqli_query ($cxn, $query) or die ("Couldn't execute query");

$query = "SELECT id_kingdom, name_kingdom FROM Kingdoms;";
$kingdoms = mysqli_query ($cxn, $query) or die ("Couldn't execute query");

$query = "SELECT id_rank, name_rank FROM Ranks;";
$ranks = mysqli_query ($cxn, $query) or die ("Couldn't execute query");

mysqli_close ($cxn); 
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
          while ($row= mysqli_fetch_array($groups)) {
                echo '<option value="'.$row["id_group"].'">'.$row["name_group"].'</option>';
          } ?>
          </td>
      </tr>
      <tr>
          <td class="text-right">Kingdom of Award<br>Required</td>
          <td><select name="id_kingdom" required></option>
          <?php 
          while ($row= mysqli_fetch_array($kingdoms)) {
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
          while ($row= mysqli_fetch_array($ranks)) {
                echo '<option value="'.$row["id_rank"].'">'.$row["name_rank"].'</option>';
          } ?>
          </td>
      </tr>
  </table>
  <input type="submit" value="Add Award Information">
</form>
    </div></div>
