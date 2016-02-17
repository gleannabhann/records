<?php
echo "
<div class='row'>
  <div class='col-md-8 col-md-offset-2'>";

// TODO Clean up the SELECT * query
$query = "SELECT * FROM Persons WHERE id_person = $id_person;";
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute query");
if (mysqli_num_rows($result)==1) {
   $person=  mysqli_fetch_array($result);
} else {
    exit_with_footer();
}
$query = "SELECT id_group, "
        . "CONCAT(name_group,' (',name_kingdom,')') as Name_Group, "
        . "Groups.id_kingdom!=".HOST_KINGDOM_ID." as In_Kingdom "
        . "FROM Groups, Kingdoms "
        . "WHERE Groups.id_kingdom = Kingdoms.id_kingdom "
        . "Order By In_Kingdom, Name_Group;";
$groups = mysqli_query ($cxn, $query) or die ("Couldn't execute query");

// Display form with all person's info.
echo '<form action="edit_person.php" method="post">';
echo form_title("Editing Personal Information");
echo button_link("person.php?id=".$id_person, "To Personal Overview Page");
echo '<input type="hidden" name="id" value="'.$person["id_person"].'">';
echo "<table class='table table-condensed table-bordered'>";
//<thead><td class='text-right'>Column</td><td class='text-left'>Value</td></thead>";


// SCA Name:
if (isset($_POST["SCA_name"])&& is_string($_POST["SCA_name"])) {
    $sca_name=$_POST["SCA_name"];
} else {
    $sca_name=$person["name_person"];
}
echo '<tr><td class="text-right">SCA Name:</td><td><input type="text" name="SCA_name" value="'
     . $sca_name.'"></td></tr>';
// Mundane Name
if (isset($_POST["mundane_name"])&& is_string($_POST["mundane_name"])) {
    $mundane_name=$_POST["mundane_name"];
} else {
    $mundane_name=$person["name_mundane_person"];
}
echo '<tr><td class="text-right">Legal Name:</td><td> <input type="text" name="mundane_name" value="'
     . $mundane_name.'"></td></tr>';
// SCA Membership Number
if (isset($_POST["mem_num"]) && is_numeric($_POST["mem_num"])) {
    $mem_num = $_POST["mem_num"];
} else {
    $mem_num = $person["membership_person"];
}
echo '<tr><td class="text-right">SCA Membership Number:</td><td> <input type="number" name="mem_num" value="'
    . $mem_num .'"></td></tr>';
// SCA Membership Expiry Date
if (isset($_POST["mem_exp"]) && is_string($_POST["mem_exp"])) {
    $mem_exp = $_POST["mem_exp"];
} else {
    $mem_exp = $person["membership_expire_person"];
}
echo '<tr><td class="text-right">expires:</td><td> <input type="date" class="date" name="mem_exp" value="'
    . $mem_exp . '"> (format if no datepicker: yyyy-mm-dd)</td></tr>';

// SCA Group (add all the possible groups to a selection box, current group is selected)
if (isset($_POST["id_group"]) && is_numeric($_POST["id_group"])) {
    $id_group = $_POST["id_group"];
} else {
    $id_group = $person["id_group"];
}
echo '<tr><td class="text-right">SCA Group:</td><td>';
echo '<select name="id_group" ><option value="0"></option>';
while ($row= mysqli_fetch_array($groups)) {
    echo '<option value="'.$row["id_group"].'"';
    if ($row["id_group"]==$id_group) echo ' selected';
    echo '>'.$row["Name_Group"].'</option>';
}
echo '</select></td></tr>';
// SCA email address
if (isset($_POST["email"]) && is_string($_POST["email"])) {
    $email = $_POST["email"];
} else {
    $email = $person["email_person"];
}
echo '<tr><td class="text-right">Email address:</td><td> <input type="email" name="email" value =" '
    . $email . '"</td></tr>';
// phone_person       
if (isset($_POST["phone"]) && is_string($_POST["phone"])) {
    $phone = $_POST["phone"];
} else {
    $phone = $person["phone_person"];
}
echo '<tr><td class="text-right">Phone Number:</td><td>'
    .  '<input type="text" name="phone" value="'.$phone.'"size="45" maxlength="45"></td>'
    .  '</tr>';
// street_person
if (isset($_POST["street"]) && is_string($_POST["street"])) {
    $street = $_POST["street"];
} else {
    $street = $person["street_person"];
}
echo '<tr><td class="text-right">Street Address:</td>'
    .'<td><input type="text" name="street" value="'.$street.'" size="50" maxlength="128"></td>'
    .'</tr>';
// city_person        
if (isset($_POST["city"]) && is_string($_POST["city"])) {
    $city = $_POST["city"];
} else {
    $city = $person["city_person"];
}
echo '<tr><td class="text-right">City:</td>'
    .'<td><input type="text" name="city" value="'.$city.'"size="45" maxlength="45"></td>'
    .'</tr>';
// state_person        
if (isset($_POST["state"]) && is_string($_POST["state"])) {
    $state = $_POST["state"];
} else {
    $state = $person["state_person"];
}
echo '<tr><td class="text-right">State:</td>'
    .'<td><input type="text" name="state" value="'.$state.'" size="2" maxlength="45"></td>'
    .'</tr>';
// zip_person
if (isset($_POST["zip"]) && is_string($_POST["zip"])) {
    $zip = $_POST["zip"];
} else {
    $zip = $person["postcode_person"];
}
echo '<tr><td class="text-right">Zip:</td>'
    .'<td><input type="text" name="zip" value="'.$zip.'"size="5" maxlength="45"></td>'
    .'</tr>';

// waiver_person
if (isset($_POST["waiver_person"]) && is_string($_POST["waiver_person"])) {
    $waiver_person = $_POST["waiver_person"];
} else {
    $waiver_person = $person["waiver_person"];
}
echo '<tr><td class="text-right">Waiver on File with Sheriff:</td><td>';
echo '<select name="waiver_person" >';
$waivers = array ('Yes', 'No', 'Parent' );
foreach ($waivers as $value ) {
    echo '<option value="'.$value.'"';
    if ($waiver_person==$value) echo ' selected';
    echo '>'.$value.'</option>';
}
echo '</td></tr>';

// youth_person
if (isset($_POST["youth_person"]) && is_string($_POST["youth_person"])) {
    $youth_person = $_POST["youth_person"];
} else {
    $youth_person = $person["youth_person"];
}
echo '<tr><td class="text-right">Youth Fighter:</td><td>';
echo '<select name="youth_person" >';
$youths = array ('Yes', 'No' );
foreach ($youths as $value ) {
    echo '<option value="'.$value.'"';
    if ($youth_person==$value) echo ' selected';
    echo '>'.$value.'</option>';
}
echo '</td></tr>';

// birthdate_person
if (isset($_POST["birthdate_person"]) && is_string($_POST["birthdate_person"])) {
    $birthdate_person = $_POST["birthdate_person"];
} else {
    $birthdate_person = $person["membership_expire_person"];
}
echo '<tr><td class="text-right">Birthdate (required for youth fighters):</td><td> <input type="date" class="date" name="birthdate_person" value="'
    . $birthdate_person . '"> (format if no datepicker: yyyy-mm-dd)</td></tr>';


echo "</table>";
echo '<input type="submit" value="Update Personal Information">';
echo '</form>';


echo "<p>";
echo "</div></div>";
//echo "Permissions for herald is ".permissions("Herald")."<br>";
//echo var_dump($_SESSION);


if (($_SERVER['REQUEST_METHOD'] == 'POST')  && (permissions("Any")>=3)){

    $update = "UPDATE Persons SET ";
    $update=$update." name_person='$sca_name'";
    if ($mundane_name!=$person["name_mundane_person"]) {
        $update=$update.", name_mundane_person='$mundane_name'";        
    }
    if ($mem_num!=$person["membership_person"]) {
        $update=$update.", membership_person='$mem_num'";        
    }
    if ($mem_exp!=$person["membership_expire_person"]) {
        $update=$update.", membership_expire_person='$mem_exp'";        
    }
    if ($id_group!=$person["id_group"]) {
        $update=$update.", id_group='$id_group'";        
    }
    if ($email!=$person["email_person"]) {
        $update=$update.", email_person='$email'";        
    }
    if ($phone!=$person["phone_person"]) {
        $update=$update.", phone_person='$phone'";        
    }
    if ($street!=$person["street_person"]) {
        $update=$update.", street_person='$street'";        
    }
    if ($city!=$person["city_person"]) {
        $update=$update.", city_person='$city'";        
    }
    if ($state!=$person["state_person"]) {
        $update=$update.", state_person='$state'";        
    }
    if ($zip!=$person["postcode_person"]) {
        $update=$update.", postcode_person='$zip'";        
    }

    if ($waiver_person!=$person["waiver_person"]) {
        $update=$update.", waiver_person='$waiver_person'";
    }
    
    if ($youth_person!=$person["youth_person"]) {
        $update=$update.", youth_person='$youth_person'";
    }
    
    if ($birthdate_person!= $person["birthdate_person"]) {
            $update=$update.", birthdate_person='$birthdate_person'";
    }
    $update=$update. " WHERE id_person=" .$id_person;
    // echo "<p>Query is " . $update . "<p>";
    $result=update_query($cxn, $update);
    if ($result !== 1) {
        echo "Error updating record: " . mysqli_error($cxn);
        if (DEBUG) { echo "<P>Query was $update<p>";}
    }
}



?>
