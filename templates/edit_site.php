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

//obtain a count of how many site records are in the db
$query = "SELECT COUNT(*) from sites";
$result = mysqli_query($cxn, $query) or die ("Couldn't execute query");
if (mysqli_num_rows($result)==1) {
   $max_item_result= mysqli_fetch_assoc($result);
} else {
    exit_with_footer();
}
//set the max_item variable based on the COUNT query
$max_item = $max_item_result['COUNT(*)'];

//start the Bootstrap row
echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";

//look up the information for the site we want to edit
$query = "SELECT * from Sites where id_site = $id_site";
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute query");
if (mysqli_num_rows($result)==1) {
   $site= mysqli_fetch_assoc($result);
} else {
    exit_with_footer();
}
//set next and previous item variables
$next_item = $id_site; //$next_item refers to the site id that occurrs numerically after the current site
$next_item++;
$previous_item = $id_site; //$previous item refers to the site id that occurs numerically prior to the current site
$previous_item--;

// NOTE: By building the site first, we've populated all the variables.
// Display form with all person's info.
echo '<form action="edit_site.php" method="post">';
echo "<h2>Editing Event Site Information</h2>";
echo '<input type="hidden" name="id" value="'.$id_site.'">';

//top navigation buttons: previous, next, return to list
echo "<div class=\"btn-group\" role=\"group\" aria-label=\"navigation\">";
//previous page
if ($previous_item >= 1) {echo "<button type=\"button\" class=\"btn btn-default\"><a href=\"./edit_site.php?id=".$previous_item."\">Previous Site</a></button>";}
//next page
if ($next_item < $max_item) {echo "<button type=\"button\" class=\"btn btn-default\"><a href=\"./edit_site.php?id=".$next_item."\">Next Site</a></button>";}
//go back to the list
echo "<button type=\"button\" class=\"btn btn-default\"><a href=\"./list_site.php\">Return to List of Sites</a></button></div>";
//open the table
echo "<table class='table table-condensed table-bordered'>";
echo "<thead><td class='text-right'>Label</td><td class='text-left'>Field</td><td>Instructions</td></thead>";

/*****************************************************************************/
$varname="name_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $name_site=$_POST[$varname];
} else {
    $name_site=$site[$varname];
}
echo '<tr><td class="text-right">Name of Site:</td><td><input type="text" '
     . 'name="'.$varname.'" size="50" maxlength="256" value="'
     . $name_site.'" required></td><td></td></tr>';
/*****************************************************************************/
$varname="url_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $url_site=$_POST[$varname];
} else {
    $url_site=$site[$varname];
}
echo '<tr><td class="text-right">URL of Site:</td><td><input type="url" '
     . 'name="'.$varname.'" size="50" maxlength="256" value="'
     .$url_site.'"></td><td>If this field is empty, please do a search to see if
     the venue has a web site. Preference is for independent web sites, but if
     all they have is a Facebook Page, that will be sufficient.</td></tr>';
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
     . $facilities_site.'</textarea></td><td>What facilities/amenities does the
     venue offer?</td></tr>';
/*****************************************************************************/
$varname="capacity_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $capacity_site=$_POST[$varname];
} else {
    $capacity_site=$site[$varname];
}
echo '<tr><td class="text-right">Capacity:</td><td><input type="number" '
     . 'name="'.$varname.'" value="'
     . $capacity_site.'"></td><td>Maximum number of people permitted</td></tr>';
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
     . $rates_site.'</textarea></td><td>Place information about fees and rates here</td></tr>';
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
     . $area_site.'</textarea></td><td>Place location description here (ie PO Box
     Mailing Address, driving directions, etc.)</td></tr>';
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
     . $contact_site.'</textarea></td><td>Information about how to contact the
     site manager. Can include telephone numbers and/or email addresses.</td></tr>';
/*****************************************************************************/
$varname="lat_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $lat_site=$_POST[$varname];
} else {
    $lat_site=$site[$varname];
}
echo '<tr><td class="text-right">Latitude:</td><td><input type="number" step="any" '
     . 'name="'.$varname.'" value="'
     . $lat_site.'"></td><td>Format: 29.1234567</td></tr>';
/*****************************************************************************/
$varname="long_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $long_site=$_POST[$varname];
} else {
    $long_site=$site[$varname];
}
echo '<tr><td class="text-right">Longitude:</td><td><input type="number" step="any" '
     . 'name="'.$varname.'"  value="'
     . $long_site.'"></td><td>Format: -90.1234567</td></tr>';
// TODO: Create button to update the lat/lng fields based on Google Maps API
// geocoding of the street address.
/*****************************************************************************/

$varname="street_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $street_site=$_POST[$varname];
} else {
    $street_site=$site[$varname];
}
echo '<tr><td class="text-right">Street:</td><td><input type="text" '
     . 'name="'.$varname.'" size="50" maxlength="256" value="'
     . $street_site.'"></td><td>Standard Postal Number and street</td></tr>';
/*****************************************************************************/
$varname="city_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $city_site=$_POST[$varname];
} else {
    $city_site=$site[$varname];
}
echo '<tr><td class="text-right">City:</td><td><input type="text" '
     . 'name="'.$varname.'" size="50" maxlength="256" value="'
     . $city_site.'"></td><td>Standard Postal Address City</td></tr>';

/*****************************************************************************/
$varname="state_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $state_site=$_POST[$varname];
} else {
    $state_site=$site[$varname];
}
echo '<tr><td class="text-right">State<br>(abbreviated):</td><td><input type="text" '
     . 'name="'.$varname.'" size="2" maxlength="2" value="'
     . $state_site.'"></td><td>State, using 2 letters and no punctuation</td></tr>';
/*****************************************************************************/
$varname="zip_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $zip_site=$_POST[$varname];
} else {
    $zip_site=$site[$varname];
}
echo '<tr><td class="text-right">Zip<br>(abbreviated):</td><td><input type="text" '
     . 'name="'.$varname.'" size="5" maxlength="5" value="'
     . $zip_site.'"></td><td>5 digit zip code.</td></tr>';
/*****************************************************************************/
$varname="active_site";
if (isset($_POST[$varname])) {
    if ($_POST[$varname]=="Yes") { $active_site=1;}
    else {$active_site=0;}
    //$active_site=$_POST[$varname];
} else {
    $active_site=$site[$varname];
}
echo '<tr><td class="text-right">Active<br>(abbreviated):</td><td><input type="checkbox" '
     . 'name="'.$varname.'" value="Yes"';
if ($active_site>0) { echo ' checked="checked" ';}
echo '></td><td>"Active" means site is available for rental for SCA events.
If site becomes unavailable due to change in management or for other reasons,
uncheck box. Listing will remain in the database in case it becomes available again
later, but will not display in the public list.</td></tr>';

echo "</table>";
echo '<input type="submit" value="Update Event Site Information">';
//echo '<button type="reset" value="Reset">Reset</button>';
echo '</form>';
/*****************************************************************************/
/*****************************************************************************/

// Add Links back to the main list, and to the next site needing to be verified.



echo "<div class=\"btn-group\" role=\"group\" aria-label=\"navigation\">";
//previous page
if ($previous_item >= 1) {echo "<button type=\"button\" class=\"btn btn-default\"><a href=\"./edit_site.php?id=".$previous_item."\">Previous Site</a></button>";}
//next page
if ($next_item < $max_item) {echo "<button type=\"button\" class=\"btn btn-default\"><a href=\"./edit_site.php?id=".$next_item."\">Next Site</a></button>";}


/* if either the lat or long variables are null, make a geocode request via the
   geocode() function.

//check to see if either lat or lng is NULL
if ($lat_site == NULL OR $long_site == NULL)
  {
    //combine the address fields into a standard USPS address
    $address = $street_site.", ".$city_site.", ".$state_site." ".$zip_site;

    //pass $address to geocode()
    $result = geocode($address);

    //store the results in the appropriate variables
    //you give one variable and it returns an array containing two items: a lat
    //value and a long value.
    $lat_site = $result["lat"];
    $long_site = $result["long"];
  }


*/

//TODO: should this query only select rows where verified_site == NULL to exclude any site that has a date stamp?
$query="SELECT id_site, verified_site from Sites order by verified_site desc, id_site;";
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute query");
if (mysqli_num_rows($result)>=1) {
   $next_site= mysqli_fetch_assoc($result);

    echo "<a href=\"./edit_site.php?id=".$next_site["id_site"]."\">
    <button type=\"button\" class=\"btn btn-default\">Next Site Needed to Verify</a></button>";
}
echo "<button type=\"button\" class=\"btn btn-default\"><a href=\"./list_site.php\">Return to List of Sites</a></button></div>";
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
    echo "<p>Query is " . $update . "<p>";
    echo "Value of $active_site is ".$active_site
            ." and from post is ".$_POST["active_site"]
            ." and from query is ".$site["active_site"]."<p>";
    $result=update_query($cxn, $update);
    if ($result !== 1) {echo "Error updating record: " . mysqli_error($cxn);}
}

mysqli_close ($cxn); /* close the db connection */
?>
