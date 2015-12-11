<?php
// Purpose: to enter data for a new site,
// Privileges needed: permissions("Any")>= 3
if ((!permissions("Herald")>=3) && (!permissions("Marshal")>=3)) {
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();    
}
if ((isset($_GET['part_name'])) && (is_string($_GET['part_name']))) {
    // We got here through the add_person link on search.php
    // echo "Arrived from person.php";
    $part_name = $_GET["part_name"];
} elseif ((isset($_POST['part_name'])) && (is_string($_POST['part_name']))) {
    // We got here from form submission
    // echo "Arrived as form submission";
    $part_name = $_POST["part_name"];
} else  {
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}
$cxn = open_db_browse();

// Build list of groups for add_person page.
if (($_SERVER['REQUEST_METHOD'] == 'POST')  && (permissions("Any")>=3)){
    //echo "Now adding ".$_POST["SCA_name"]." to the database.<br>";

    $query_head = "INSERT INTO Persons(name_person";
    $query_tail = " VALUES(";
    
    // Since SCA name is required, we can assume it's set
    $sca_name = sanitize_mysql($_POST["SCA_name"]);
    $query_tail = $query_tail."'$sca_name'";
    
    //mundane_name -> name_mundane_person
    if (isset($_POST["mundane_name"])
            && (!empty($_POST["mundane_name"])) 
            && (is_string($_POST["mundane_name"]))) {
        $mundane_name = sanitize_mysql($_POST["mundane_name"]);
        $query_head = $query_head.",name_mundane_person";
        $query_tail = $query_tail.",'$mundane_name'";            
    }
    //email -> email_person
    if (isset($_POST["email"])
            && (!empty($_POST["email"])) 
            && (is_string($_POST["email"]))) {
        $email = sanitize_mysql($_POST["email"]);
        $query_head = $query_head.",email_person";
        $query_tail = $query_tail.",'$email'";            
    }
    //mem_num -> membership_person (number)
    if (isset($_POST["mem_num"]) 
            && (!empty($_POST["mem_num"])) 
            && (is_numeric($_POST["mem_num"]))) {
            $mem_num = $_POST["mem_num"];
            $query_head = $query_head.",membership_person";
            $query_tail = $query_tail.",$mem_num";
       }
       
    //mem_exp -> membership_expire_person (date)
    if (isset($_POST["mem_exp"])
        && (!empty($_POST["mem_exp"]))
        && (is_string($_POST["mem_exp"]))){
        $mem_exp=$_POST["mem_exp"];
        $query_head = $query_head.",membership_expire_person";
        $query_tail = $query_tail.",'$mem_exp'";
    }
        
    //id_group -> id_group
    if (isset($_POST["id_group"])
        && (!empty($_POST["id_group"]))
        && (is_numeric($_POST["id_group"]))){
        $id_group = $_POST["id_group"];
        $query_head = $query_head.",id_group";
        $query_tail = $query_tail.",$id_group";
    }  
    
    // phone -> phone_person
    if (isset($_POST["phone"])
            && !empty($_POST["phone"])
            && (is_string($_POST["phone"]))){
        $phone = sanitize_mysql($_POST["phone"]);
        $query_head = $query_head.",phone_person";
        $query_tail = $query_tail.",$phone";
    }
        
    // street -> street_person
    if (isset($_POST["street"])
            && !empty($_POST["street"])
            && (is_string($_POST["street"]))){
        $street = sanitize_mysql($_POST["street"]);
        $query_head = $query_head.",street_person";
        $query_tail = $query_tail.",$street";
    }
        
    // city -> city_person
    if (isset($_POST["city"])
            && !empty($_POST["city"])
            && (is_string($_POST["city"]))){
        $city = sanitize_mysql($_POST["city"]);
        $query_head = $query_head.",city_person";
        $query_tail = $query_tail.",$city";
    }
        
    // state -> state_person
    if (isset($_POST["state"])
            && !empty($_POST["state"])
            && (is_string($_POST["state"]))){
        $state = sanitize_mysql($_POST["state"]);
        $query_head = $query_head.",state_person";
        $query_tail = $query_tail.",$state";
    }
        
    // zip -> postcode_person
    if (isset($_POST["zip"])
            && !empty($_POST["zip"])
            && (is_string($_POST["zip"]))){
        $zip = sanitize_mysql($_POST["zip"]);
        $query_head = $query_head.",postcode_person";
        $query_tail = $query_tail.",$zip";
    }
        
    
    
    $query_head=$query_head.",active_person)";
    $query_tail=$query_tail.",1);";
    //echo "Query is:<br>".$query_head."<br>".$query_tail."</p>";
    $query = $query_head.$query_tail;
    $result=update_query($cxn, $query);
    if ($result !== 1) {
           echo "Error updating record: " . mysqli_error($cxn);
    } else {
           echo "Successfully added $sca_name to the Database.<p>\n";
           $query = "SELECT id_person from Persons where name_person='$sca_name';";
           $result = mysqli_query ($cxn, $query) or die ("Couldn't execute query");
           $person=  mysqli_fetch_array($result);
           $id_person=$person["id_person"];
           echo button_link("edit_person.php?id=$id_person", "Go To Edit Awards for $sca_name");
           echo '<p>or continue adding new persons below:';
    }

}

$query = "SELECT id_group, "
        . "CONCAT(name_group,' (',name_kingdom,')') as Name_Group, "
        . "Groups.id_kingdom!=".HOST_KINGDOM_ID." as In_Kingdom "
        . "FROM Groups, Kingdoms "
        . "WHERE Groups.id_kingdom = Kingdoms.id_kingdom "
        . "Order By In_Kingdom, Name_Group;";
$groups = mysqli_query ($cxn, $query) or die ("Couldn't execute query");

?>

<div class='row'><div class='col-md-8 col-md-offset-2'>
<form action="add_person.php" method="post">
   <?php 
         echo form_title("Adding a New Person")."\n";
         echo button_link("search.php?name=".$part_name, "Return to Search Page");
         echo '<input type="hidden" name="part_name" value="'.$part_name.'">'; ?>
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
              while ($row= mysqli_fetch_array($groups)) {
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
mysqli_close ($cxn); /* close the db connection */
?>