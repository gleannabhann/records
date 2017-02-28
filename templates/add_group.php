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
    
    $query_head = "INSERT INTO Groups(name_group,id_kingdom) ";
    $query_tail = " VALUES('".  sanitize_mysql($_POST["name_group"])."'";    
    if (isset($_POST["id_kingdom"])
    && (!empty($_POST["id_kingdom"]))
    && (is_numeric($_POST["id_kingdom"]))){
        $query_tail=$query_tail.",".$_POST["id_kingdom"];
    } else {
        $query_tail=$query_tail.",0";
    }

    $query = $query_head." ".$query_tail.");";
    if (DEBUG) { echo "Insert Query is:<br>$query<p>"; }
    $result=update_query($cxn, $query);
    if ($result !== 1) {
        echo "Error updating record: " . mysqli_error($cxn);
    } else {
        echo "Successfully added ".$_POST["name_group"]." to the database<p>";
        echo button_link("awards.php", "Return to awards page");
        echo "or continue adding new groups below<p>";
    }
}

// Set up the data base queries to populate the form

$query = "SELECT id_kingdom, name_kingdom FROM Kingdoms;";
$kingdoms = mysqli_query ($cxn, $query) or die ("Couldn't execute query");

mysqli_close ($cxn); 
?>

<div class='row'><div class='col-md-8 col-md-offset-2'>
<form action="add_group.php" method="post">
  <?php echo form_title("Adding a New Group"); ?>
  <table class='table table-condensed table-bordered'>
      <tr>
          <td class="text-right">Group Name<br>(Required)</td>
          <td><input type="text" name="name_group" size="50" maxlength="128" required>
          </td>
      </tr>
      <tr>
          <td class="text-right">Kingdom <br>Required</td>
          <td><select name="id_kingdom" required></option>
          <?php 
          while ($row= mysqli_fetch_array($kingdoms)) {
              echo '<option value="'.$row["id_kingdom"].'"';
              if ($row["id_kingdom"]==HOST_KINGDOM_ID) echo ' selected';
              echo '>'.$row["name_kingdom"].'</option>';
          } ?>
          </td>
      </tr>
  </table>
  <input type="submit" value="Add New Group">
</form>
    </div></div>
