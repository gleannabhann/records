<?php
// Purpose: to enter data for a new site,
// Privileges needed: permissions("Any")>= 3
if ((!permissions("Herald")>=3) && (!permissions("Marshal")>=3)) {
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();    
}
if ((isset($_GET['part_name'])) && (is_string($_GET['part_name']))) {
    // Path: Search page -> add_person.php
    // echo "Arrived from person.php";
    $part_name = $_GET["part_name"];
} elseif ((isset($_POST['part_name'])) && is_string($_POST['part_name']))  {
    // path: Search page -> add_person.php -> form submission
    // echo "Arrived as form submission";
    $part_name = $_POST["part_name"];
} 
// if we arrived via $_POST without having $_POST['part_name'] it means 
// the user arrived via the "add_person" link on the navbar, which is 
// available to anyone logged in with the correct perms. We won't set
// $part_name, and when we build the form, we won't include it


/* header.php and header_main.php open the db connection for us */

// Build list of groups for add_person page.
if (($_SERVER['REQUEST_METHOD'] == 'POST')  && (permissions("Any")>=3)){
    //echo "Now adding ".$_POST["SCA_name"]." to the database.<br>";
    $data = [];
    $query_head = "INSERT INTO Persons(name_person";
    $query_tail = " VALUES(";
    
    // Since SCA name is required, we can assume it's set
    $sca_name = $_POST["SCA_name"];
    $query_tail = $query_tail.":name_person";
    $data[':name_person'] = $sca_name;

    //mundane_name -> name_mundane_person
    if (isset($_POST["mundane_name"])
            && (!empty($_POST["mundane_name"])) 
            && (is_string($_POST["mundane_name"]))) {
        $mundane_name = ($_POST["mundane_name"]);
        $query_head = $query_head.",name_mundane_person";
        $query_tail = $query_tail.",:mundane_name";            
        $data[':mundane_name'] = $mundane_name; // PII
    }
    //email -> email_person
    if (isset($_POST["email"])
            && (!empty($_POST["email"])) 
            && (is_string($_POST["email"]))) {
        $email = ($_POST["email"]);
        $query_head = $query_head.",email_person";
        $query_tail = $query_tail.",:email";            
        $data[':email'] = $email;
    }
    //mem_num -> membership_person (number)
    if (isset($_POST["mem_num"]) 
            && (!empty($_POST["mem_num"])) 
            && (is_numeric($_POST["mem_num"]))) {
            $mem_num = $_POST["mem_num"];
            $query_head = $query_head.",membership_person";
            $query_tail = $query_tail.",:mem_num";
            $data[':mem_num'] = $mem_num;
       }
       
    //mem_exp -> membership_expire_person (date)
    if (isset($_POST["mem_exp"])
        && (!empty($_POST["mem_exp"]))
        && (is_string($_POST["mem_exp"]))){
        $mem_exp=$_POST["mem_exp"];
        $query_head = $query_head.",membership_expire_person";
        $query_tail = $query_tail.",:mem_exp";
        $data[':mem_exp'] = $mem_exp;
    }
        
    //id_group -> id_group
    if (isset($_POST["id_group"])
        && (!empty($_POST["id_group"]))
        && (is_numeric($_POST["id_group"]))){
        $id_group = $_POST["id_group"];
        $query_head = $query_head.",id_group";
        $query_tail = $query_tail.",:id_group";
        $data[':id_group'] = $id_group;
    }  
    
    // phone -> phone_person
    if (isset($_POST["phone"])
            && !empty($_POST["phone"])
            && (is_string($_POST["phone"]))){
        $phone = ($_POST["phone"]);
        $query_head = $query_head.",phone_person";
        $query_tail = $query_tail.",:phone";
        $data[':phone'] = $phone;
    }
        
    // street -> street_person
    if (isset($_POST["street"])
            && !empty($_POST["street"])
            && (is_string($_POST["street"]))){
        $street = ($_POST["street"]);
        $query_head = $query_head.",street_person";
        $query_tail = $query_tail.",:street";
        $data[':street'] = $street;
    }
        
    // city -> city_person
    if (isset($_POST["city"])
            && !empty($_POST["city"])
            && (is_string($_POST["city"]))){
        $city = ($_POST["city"]);
        $query_head = $query_head.",city_person";
        $query_tail = $query_tail.",:city";
        $data[':city'] = $city;
    }
        
    // state -> state_person
    if (isset($_POST["state"])
            && !empty($_POST["state"])
            && (is_string($_POST["state"]))){
        $state = sanitize_mysql($_POST["state"]);
        $query_head = $query_head.",state_person";
        $query_tail = $query_tail.",:state";
        $data[':state'] = $state;
    }
        
    // zip -> postcode_person
    // TODO convert "zip" to "postcode" everywhere
    if (isset($_POST["zip"])
            && !empty($_POST["zip"])
            && (is_string($_POST["zip"]))){
        $zip = ($_POST["zip"]);
        $query_head = $query_head.",postcode_person";
        $query_tail = $query_tail.",:zip";
        $data[':zip'] = $zip;
    }
        
    $query_head=$query_head.")";
    $query_tail=$query_tail.");";
    //echo "Query is:<br>".$query_head."<br>".$query_tail."</p>";
    $query = $query_head.$query_tail;
    
    try {
      $result=update_query($cxn, $query, $data);
      $message = "Successfully added <strong>$sca_name</strong> to the Database.";   
      echo "<div class='row'><div class='col-sm-12 col-md-8 col-md-offset-2'>";
      bs_alert($message, 'success');
      echo "</div></div>";
    } catch (PDOException $e) {
      $f_msg = "I couldn't add $sca_name to the database.";
      bs_alert($f_msg, 'danger');
      if (DEBUG) {
        $msg = "add_person.php failed to insert new person";
        // check for PII. Repeat this block for each array keypair in $data as
        // needed to remove selected PII before sending to the debug log
        if (isset($data[':mundane_name'])) {
          // redact PII
          $data[':mundane_name'] = 'redacted';
        }
        $exc = ['exc_msg' => $e->getMessage(), 'exc_code' => $e->getCode()];
        $vars = ['query' => $query, 'data' => $data, 'exception' => $exc];
        $arr = ['message' => $msg, 'vars' => $vars];
        $message = json_encode($arr) . "\n";
        error_log($message, 3, DEBUG_DEST);
      }
      exit_with_footer();
    }
   
    // pull the id for the person we just added
    $query = "SELECT id_person from Persons where name_person=:sca_name;";
    $data = [':sca_name' => $sca_name];
    try {
      $sth = $cxn->prepare($query);
      $sth->execute($data);
      $person = $sth->fetch();
      $vars = json_encode($person) . '\n';  
      error_log($vars, 3, DEBUG_DEST);
      $id_person = $person["id_person"];
      $message = button_link("edit_person.php?id=$id_person", "Go To Edit Person for $sca_name")
        . '</p><p class="text-center">or continue adding new persons below:';
      bs_alert($message, 'info');
    } catch (PDOException $e) {
      // if the associated try block fails, it's because something went wrong
      // with the insert that was performed before this block was executed.
      // That should be reported loudly, as it shouldn't happen.
      $f_message = "Something went wrong. An administrator has been notified.";
      bs_alert($f_message, "danger");
      $date = date("Y-m-d H:i:s");
      $msg = "add_person.php failed to fetch newly inserted person details";
      $exc = ['exc_msg' => $e->getMessage(), 'exc_code' => $e->getCode()];
      $vars = ['query' => $query, 'data' => $data, 'exception' => $exc];
      $arr = ['date' => $date, 'message' => $msg, 'vars' => $vars];
      $message = json_encode($arr) . "\n";
      if (DEBUG) {
        error_log($message, 3, DEBUG_DEST);
      }
      // when dev email notifications are set up, uncomment this line:
      //error_log($message, 2, DEV_EMAIL_DEST)
    }

}
$k_query = "SELECT host_kingdom_id FROM Appdata WHERE app_id=1";
try {
  $sth = $cxn->query($k_query);
  $k_id = $sth->fetch();
  $query = "SELECT id_group, "
        . "CONCAT(name_group,' (',name_kingdom,')') as Name_Group, "
        . "Groups.id_kingdom!=:k_id as In_Kingdom "
        . "FROM Groups, Kingdoms "
        . "WHERE Groups.id_kingdom = Kingdoms.id_kingdom "
        . "Order By In_Kingdom, Name_Group;";
  $data = [':k_id' => $k_id['host_kingdom_id']];
  $sth = $cxn->prepare($query);
  $sth->execute($data);
  } catch (PDOException $e) {
    $msg = "add_person.php: Failed to fetch host kingdom id";
    $exc = ['exc_msg' => $e->getMessage(), 'exc_code' => $e->getCode()];
    $vars = ['k_query' => $k_query, 'exc' => $exc];
    if ($data) {
      // if $data exists, $query must also, let's add them both
      $vars['query'] = $query;
      $vars['data'] = $data;
    }
    $arr = ['message' => $msg, 'vars' => $vars];
    $message = json_encode($arr) . "\n";
    if (DEBUG) {
      // send to the debug log
      error_log($message, 3, DEBUG_DEST);
    } elseif (!isset($data)) {
      // this is a critical error, let's send it to the regular error log
      // TODO this one should probably qualify for generating an email to a dev
      // but only if $data doesn't exist (no $data = the failure was with
      // fetching the host kingdom ID, which does not depend on user input)
      error_log($message);
      // error_log($message, 2, DEV_ERR_DEST)
    }
  }

?>

<div class='row'><div class='col-md-8 col-md-offset-2'>
<form action="add_person.php" method="post">
<?php 
  echo form_title("Adding a New Person")."\n";
  if (isset($part_name)) {
    // the user originally came from the search page, so let's allow them to
    // return to it, and pass the var for that on to the form so it is
    // persistent even if they add multiple people.
    echo button_link("search.php?name=".$part_name, "Return to Search Page");
    echo '<input type="hidden" name="part_name" value="'.$part_name.'">'; 
  }
?>
  
   <table class='table table-condensed table-bordered'>
      <tr>
          <td class="text-right">SCA Name:<br>(required)</td>
          <td><input type="text" name="SCA_name" size="50" maxlength="128" required></td>
      </tr>
      <tr>
          <td class="text-right">Legal Name:</td>
          <td><input type="text" name="mundane_name" size="50" maxlength="128"></td>
      </tr>
          <td class="text-right">SCA Membership #:<br>(required)</td>
          <td><input type="number" name="mem_num" min="1" step="1"></td>
      </tr>
      <tr>
           <td class="text-right">expires:</td>
           <td> <input type="date" class="date" name="mem_exp"> (format if no datepicker: yyyy-mm-dd)</td>
      </tr>
      <tr>
          <td class="text-right">SCA Group:</td>
          <td><select name="id_group" >
                  <option value="0"></option>
              <?php 
              while ($row= $sth->fetch()) {
                echo '<option value="'.$row["id_group"].'"';
                echo '>'.$row["Name_Group"].'</option>';
                }
              ?>
              </select></td>
      </tr>
      <tr>
          <td class="text-right">Email Address:</td>
          <td><input type="email" name="email" size="50" maxlength="128"></td>
      </tr>
      <tr>
          <td class="text-right">Phone Number:</td>
          <td><input type="text" name="phone" size="45" maxlength="45"></td>
      </tr>
      <tr>
          <td class="text-right">Street Address:</td>
          <td><input type="text" name="street" size="50" maxlength="128"></td>
      </tr>
      <tr>
          <td class="text-right">City:</td>
          <td><input type="text" name="city" size="45" maxlength="45"></td>
      </tr>
      <tr>
          <td class="text-right">State:</td>
          <td><input type="text" name="state" size="2" maxlength="45"></td>
      </tr>
      <tr>
          <td class="text-right">Zip:</td>
          <td><input type="text" name="zip" size="5" maxlength="45"></td>
      </tr>         
  </table>
  <input type="submit" value="Add Person to Database">
</form>  
</div><!-- ./col-md-8 --></div><!-- ./row -->  

<?php
/* footer.php closes the db connection */
?>
