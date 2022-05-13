<?php

if (permissions("Herald")<3) {
    // We don't have permission to add awards so let's just exit now.
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}

if (($_SERVER['REQUEST_METHOD'] == 'POST') && (permissions("Herald")>=3)) {
    // We have a form submission.
    // Note: we allow for addition of multiple awards which is why the blank
    //       award form will reappear at the bottom of the page

    $query_head = "INSERT INTO Groups(name_group,id_kingdom) ";
    $query_tail = " VALUES(:name_group";
    $data = [':name_group' => $_POST['name_group']];
    if (isset($_POST["id_kingdom"])
    && (!empty($_POST["id_kingdom"]))
    && (is_numeric($_POST["id_kingdom"]))) {
        $query_tail=$query_tail. ",:id_kingdom";
        $data[':id_kingdom'] = $_POST["id_kingdom"];
    } else {
        $query_tail=$query_tail.",0";
    }

    $query = $query_head." ".$query_tail.");";

    try {
        $result=update_query($cxn, $query, $data);
        // bs_alert() will add our leading and trailing <p>/</p>, so we don't
        // need to put them here
        echo "<div class='row'><div class='col-sm-12 col-md-8 col-md-offset-2'>";
        $success = "Successfully added <strong>".$_POST["name_group"]."</strong> to the database</p><p class='text-center'>"
          . button_link("awards.php", "Return to awards page")
          . "</p><p class='text-center'>or continue adding new groups below";
        bs_alert($success, 'success');
        echo "</div></div>";
    } catch (PDOException $e) {
        $msg = "add_group.php insert query failed";
        $exc = ['exc_msg' => $e->getMessage(), 'exc_code' => $e->getCode()];
        $vars = ['query' => $query, 'data' => $data, 'exc' => $exc];
        $arr = ['message' => $msg, 'vars' => $vars];
        $message = json_encode($arr) . "\n";
        error_log($message, 3, DEBUG_DEST);
    }
}
// Set up the data base queries to populate the form

    $query = "SELECT id_kingdom, name_kingdom FROM Kingdoms;";
    $k_query = "SELECT host_kingdom_id FROM Appdata WHERE app_id=1";
try {
    $sth = $cxn->query($query);
    $k_sth = $cxn->query($k_query);
} catch (PDOException $e) {
    $f_message = "Could not fetch the list of Kingdoms to build the form.";
    $msg = "add_group.php select kingdoms exception";
    $exc = ['exc_msg' => $e->getMessage(), 'exc_code' => $e->getCode()];
    $vars = ['query' => $query, 'k_query' => $k_query, 'exc' => $exc];
    $arr = ['message' => $msg, 'vars' => $vars];
    $message = json_encode($arr) . "\n";
    error_log($message, 3, DEBUG_DEST);
}
$k_id = $k_sth->fetch();
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
          while ($row= $sth->fetch()) {
              echo '<option value="'.$row["id_kingdom"].'"';
              if ($row["id_kingdom"]==$k_id['host_kingdom_id']) {
                  echo ' selected';
              }
              echo '>'.$row["name_kingdom"].'</option>';
          } ?>
          </td>
      </tr>
  </table>
  <input type="submit" value="Add New Group">
</form>
    </div></div>
