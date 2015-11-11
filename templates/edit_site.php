<?php
// Purpose: to display all data for event site we're about to edit,
// Privileges needed: permissions("Sites")>= 3

if (permissions("Sites")>= 3) {
   if ((isset($_GET['id'])) && (is_numeric($_GET['id'])) && (isset($_SESSION['id']))) {
       // We got here through the edit link on list_site.php
       $id_site = $_GET["id"];
   } elseif ((isset($_POST['id'])) && (is_numeric($_POST['id'])) && (isset($_SESSION['id']))) {
       // We got here from form submission
       // echo "Arrived as form submission";
       $id_site = $_POST['id'];
   }
} else {
    // We don't have sufficient permissions for this page.
    echo '<p class="error"> This page has been accessed in error.</p>';
    echo 'Please use your back arrow to return to the previous page.';
    exit_with_footer();
}

$cxn = open_db_browse();

echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";

$query = "SELECT * from Sites where id_site = $id_site;";
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute query");
if (mysqli_num_rows($result)==1) {
   $site= mysqli_fetch_assoc($result);
} else {
    exit_with_footer();
}
// NOTE: By building the site first, we've populated all the variables.
// Display form with all person's info.
echo '<form action="edit_site.php" method="post">';
echo "<h2>Editing Event Site Information</h2>";
//echo '<a href="list_site.php">Exit Edit Page</a>';
echo '<input type="hidden" name="id" value="'.$id_site.'">';
echo "<table class='table table-condensed table-bordered'>";
//<thead><td class='text-right'>Column</td><td class='text-left'>Value</td></thead>";

/*****************************************************************************/
$varname="name_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $name_site=$_POST[$varname];
} else {
    $name_site=$site[$varname];
}
echo '<tr><td class="text-right">Name of Site:</td><td><input type="text" '
     . 'name="'.$varname.'" size="50" maxlength="256" value="'
     . $name_site.'" required></td></tr>';
/*****************************************************************************/
$varname="url_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $url_site=$_POST[$varname];
} else {
    $url_site=$site[$varname];
}
echo '<tr><td class="text-right">URL of Site:</td><td><input type="url" '
     . 'name="'.$varname.'" size="50" maxlength="256" value="'
     .$url_site.'"></td></tr>';
/*****************************************************************************/
$varname="facilities_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $facilities_site=$_POST[$varname];
} else {
    $facilities_site=$site[$varname];
}
echo '<tr><td class="text-right">Facilities:</td>"'
     . '<td><textarea '
     . 'name="'.$varname.'" rows="3" cols="50">'
     . $facilities_site.'</textarea></td></tr>';
/*****************************************************************************/
$varname="capacity_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $capacity_site=$_POST[$varname];
} else {
    $capacity_site=$site[$varname];
}
echo '<tr><td class="text-right">Capacity:</td><td><input type="number" '
     . 'name="'.$varname.'" value="'
     . $capacity_site.'"></td></tr>';
/*****************************************************************************/
$varname="rates_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $rates_site=$_POST[$varname];
} else {
    $rates_site=$site[$varname];
}
echo '<tr><td class="text-right">Rates:</td>'
     . '<td><textarea '
     . 'name="'.$varname.'" rows="3" cols="50">'
     . $rates_site.'</textarea></td></tr>';
/*****************************************************************************/
$varname="area_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $area_site=$_POST[$varname];
} else {
    $area_site=$site[$varname];
}
echo '<tr><td class="text-right">Area<br> (Deprecated by address):</td>'
     . '<td><textarea '
     . 'name="'.$varname.'" rows="2" cols="50">'
     . $area_site.'</textarea></td></tr>';
/*****************************************************************************/
$varname="contact_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $contact_site=$_POST[$varname];
} else {
    $contact_site=$site[$varname];
}
echo '<tr><td class="text-right">Contact Info:</td>'
     . '<td><textarea '
     . 'name="'.$varname.'" rows="2" cols="50">'
     . $contact_site.'</textarea></td></tr>';
/*****************************************************************************/
$varname="lat_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $lat_site=$_POST[$varname];
} else {
    $lat_site=$site[$varname];
}
echo '<tr><td class="text-right">Latitude:</td><td><input type="number" step="any" '
     . 'name="'.$varname.'" value="'
     . $lat_site.'"></td></tr>';
/*****************************************************************************/
$varname="long_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $long_site=$_POST[$varname];
} else {
    $long_site=$site[$varname];
}
echo '<tr><td class="text-right">Longitude:</td><td><input type="number" step="any" '
     . 'name="'.$varname.'"  value="'
     . $long_site.'"></td></tr>';
/*****************************************************************************/
$varname="street_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $street_site=$_POST[$varname];
} else {
    $street_site=$site[$varname];
}
echo '<tr><td class="text-right">Street:</td><td><input type="text" '
     . 'name="'.$varname.'" size="50" maxlength="256" value="'
     . $street_site.'"></td></tr>';
/*****************************************************************************/
$varname="city_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $city_site=$_POST[$varname];
} else {
    $city_site=$site[$varname];
}
echo '<tr><td class="text-right">City:</td><td><input type="text" '
     . 'name="'.$varname.'" size="50" maxlength="256" value="'
     . $city_site.'"></td></tr>';

/*****************************************************************************/
$varname="state_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $state_site=$_POST[$varname];
} else {
    $state_site=$site[$varname];
}
echo '<tr><td class="text-right">State<br>(abbreviated):</td><td><input type="text" '
     . 'name="'.$varname.'" size="2" maxlength="2" value="'
     . $state_site.'"></td></tr>';
/*****************************************************************************/
$varname="zip_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $zip_site=$_POST[$varname];
} else {
    $zip_site=$site[$varname];
}
echo '<tr><td class="text-right">Zip<br>(abbreviated):</td><td><input type="text" '
     . 'name="'.$varname.'" size="5" maxlength="5" value="'
     . $zip_site.'"></td></tr>';
/*****************************************************************************/
$varname="active_site";
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if (isset($_POST[$varname])) {
        $active_site=1;
    } else {
        $active_site=0;
    }
} else {
    $active_site=$site[$varname];
}
echo '<tr><td class="text-right">Active<br>(abbreviated):</td><td><input type="checkbox" '
     . 'name="'.$varname.'" value="Yes"';
if ($active_site>0) { echo ' checked="checked" ';}
echo '></td></tr>';

echo "</table>";
echo '<input type="submit" value="Update Event Site Information">';
//echo '<button type="reset" value="Reset">Reset</button>';
echo '</form>';
/*****************************************************************************/
/*****************************************************************************/

// Add Links back to the main list, and to the next site needing to be verified.
echo "<a href=\"./list_site.php\">Return to List of Sites</a><p>";

$query="SELECT id_site, verified_site from Sites order by verified_site desc, id_site;";
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute query");
if (mysqli_num_rows($result)>=1) {
   $next_site= mysqli_fetch_assoc($result);
    echo "<a href=\"./edit_site.php?id=".$next_site["id_site"]."\">Next Site Needed to Verify</a><p>";
}

echo "</div><!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs

// Now that the variables are all populated, 
// let's go ahead and update the database if the Update button was pressed.
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
// Process form by updating the database

    $update = "UPDATE Sites SET ";
    if (!empty($name_site))
        { $update=$update . "name_site='" . mysqli_real_escape_string($cxn,$name_site) . "'" ;}
    if ($url_site != $site["url_site"] ) 
        {$update=$update . ", url_site='" . mysqli_real_escape_string($cxn,$url_site) ."' ";}
    if ($facilities_site!= $site["facilities_site"]) 
        {$update=$update . ", facilities_site='" . mysqli_real_escape_string($cxn,$facilities_site) ."' ";}
    if ($capacity_site !=$site["capacity_site"]) 
        {if ($capacity_site > 0) 
            {$update=$update . ", capacity_site=" . $capacity_site ." ";}
            else {$update=$update . ", capacity_site=NULL ";}
        }
    if ($rates_site != $site["rates_site"]) 
        {$update=$update . ", rates_site='" . mysqli_real_escape_string($cxn,$rates_site) ."' ";}
    if ($area_site != $site["area_site"]) 
        {$update=$update . ", area_site='" . mysqli_real_escape_string($cxn,$area_site) ."' ";}
    if ($contact_site != $site["contact_site"]) 
        {$update=$update . ", contact_site='" . mysqli_real_escape_string($cxn,$contact_site) ."' ";}
    if ($lat_site !=$site["lat_site"]) 
        {if (!empty($lat_site)) 
            {$update=$update . ", lat_site=" . $lat_site ." ";}
            else {$update=$update . ", lat_site=NULL ";}
        }
    if ($long_site !=$site["long_site"]) 
        {if (!empty($long_site)) 
            {$update=$update . ", long_site=" . $long_site ." ";}
            else {$update=$update . ", long_site=NULL ";}
        }
    if ($street_site!= $site["street_site"]) 
        {$update=$update . ", street_site='" . mysqli_real_escape_string($cxn,$street_site) ."' ";}
    if ($city_site != $site["city_site"]) 
        {$update=$update . ", city_site='" . mysqli_real_escape_string($cxn,$city_site) ."' ";}
    if ($state_site != $site["state_site"]) 
        {$update=$update . ", state_site='" . mysqli_real_escape_string($cxn,$state_site) ."' ";}
    if ($zip_site!= $site["zip_site"]) 
        {$update=$update . ", zip_site='" . mysqli_real_escape_string($cxn,$zip_site) ."' ";}
    if ($active_site!= $site["active_site"]) 
        {$update=$update . ", active_site=$active_site ";}
    $update=$update. ", verified_site=curdate() WHERE id_site=" .$id_site;
//    echo "<p>Query is " . $update . "<p>";
//    echo "Value of $active_site is ".$active_site
//            ." and from query is ".$site["active_site"]."<p>";
    $result=update_query($cxn, $update);
    if ($result !== 1) {echo "Error updating record: " . mysqli_error($cxn);}
}

mysqli_close ($cxn); /* close the db connection */
?>
