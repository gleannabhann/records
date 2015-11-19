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
$query = "SELECT COUNT(*) from Sites";
$result = mysqli_query($cxn, $query) or die ("Couldn't execute query to find max count");
if (mysqli_num_rows($result)==1) {
   $max_item_result= mysqli_fetch_assoc($result);
} else {
    exit_with_footer();
}
//set the max_item variable based on the COUNT query
$max_item = $max_item_result['COUNT(*)'];

//start the Bootstrap row
echo "<div class='row'><div class='col-md-6 col-md-offset-3 col-xs-12 col-sm-12'>\n";

//look up the information for the site we want to edit
$query = "SELECT * from Sites where id_site = $id_site";
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute query to find site info");
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

/* if either the lat or long variables are null, and $street_site is set, make a geocode request via the
*  geocode() function.
*/

//check to see if either lat or lng is NULL
if (($site['lat_site'] == NULL OR $site['long_site'] == NULL) && (isset($site['street_site'])))
  {
    //combine the address fields into a standard USPS address
    $address = $site['street_site'].", ".$site['city_site'].", ".$site['state_site']." ".$site['zip_site'];

    //pass $address to geocode()
    $result = geocode($address);
    //store the results in the appropriate variables
    //you give one variable and it returns an array containing two items: a lat
    //value and a long value.
    $lat_site = $result[0];
    $long_site = $result[1];
    $coord_flag = true;
  } else {$coord_flag = false;}

//top navigation buttons: previous, next, return to list
echo "<div class=\"btn-group\" role=\"group\" aria-label=\"navigation\">\n";
//previous page
if ($previous_item >= 1) {
    echo button_link("./edit_site.php?id=".$previous_item, "Previous Site")."\n";
}
//next page
if ($next_item < $max_item) {
    echo button_link("./edit_site.php?id=".$next_item, "Next Site")."\n";
}
echo button_link("./list_site.php", "Return to List of Sites")."\n";
echo "</div><!-- class btn-group -->\n";
// NOTE: By building the site first, we've populated all the variables.
// Display form with all person's info.
echo form_title("Editing Event Site Information")."\n";

echo "<div class='well danger'><p class='danger'>Caution: Do not enter P.O. Box addresses into the
Street Address field. Place them in the Area field, and type 'None' into the
Street Address field. </p><p>You will also need to manually enter latitude and
longitude coordinates for sites without street addresses.</p></div>";

if ($coord_flag == TRUE) {
  echo "<div class='well danger'><p class='danger'>NOTICE! Latitude and Longitude
  coordinates were auto-generated for this site. Please click the \"Update Event
  Site Information\" button before you leave this page.<p></div>";
}
//open the form

echo "<form class='form-horizontal' action=\"./edit_site.php?id=".$id_site."\" method=\"post\">\n";
echo '<input type="hidden" name="id" value="'.$id_site.'"'.">\n";


/*****************************************************************************/
$varname="name_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $name_site=$_POST[$varname];
} else {
    $name_site=$site[$varname];
}
echo '<div class="form-group"><label for='.$varname.'>Name of Site:</label><input type="text" '
     . 'name="'.$varname.'" maxlength="256" value="'
     . $name_site.'" required>'
     . '<br/>This field is required</div>'."\n";
/*****************************************************************************/
$varname="url_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $url_site=$_POST[$varname];
} else {
    $url_site=$site[$varname];
}
echo '<div class="form-group"><label for='.$varname.'>URL of Site:</label><input type="url" '
     . 'name="'.$varname.'" maxlength="256" value="'
     .$url_site.'"> <br/>If this field is empty, please do a search to see if
     the venue has a web site. Preference is for independent web sites, but if
     all they have is a Facebook Page, that will be sufficient.</div>'
        ."\n";
/*****************************************************************************/
$varname="facilities_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $facilities_site=$_POST[$varname];
} else {
    $facilities_site=$site[$varname];
}
echo '<div class="form-group"><label for='.$varname.'>Facilities:</label>'
     . '<textarea '
     . 'name="'.$varname.'" rows="3">'
     . $facilities_site.'</textarea> <br/>What facilities/amenities does the
     venue offer?</div>'."\n";
/*****************************************************************************/
$varname="capacity_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $capacity_site=$_POST[$varname];
} else {
    $capacity_site=$site[$varname];
}
echo '<div class="form-group"><label for='.$varname.'>Capacity:</label><input type="number" '
     . 'name="'.$varname.'" value="'
     . $capacity_site.'"> <br/>Maximum number of people permitted</div>'."\n";
/*****************************************************************************/
$varname="rates_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $rates_site=$_POST[$varname];
} else {
    $rates_site=$site[$varname];
}
echo '<div class="form-group"><label for='.$varname.'>Rates:</label>'
     . '<textarea '
     . 'name="'.$varname.'" rows="3">'
     . $rates_site.'</textarea> <br/>Place information about fees and rates here.</div>'."\n";


/*****************************************************************************/
$varname="area_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $area_site=$_POST[$varname];
} else {
    $area_site=$site[$varname];
}
echo '<div class="form-group"><label for='.$varname.'>Area:</label>'
     . '<textarea '
     . 'name="'.$varname.'" rows="2">'
     . $area_site.'</textarea> <br/>Place location description here (ie PO Box
     Mailing Address, driving directions, etc.).</div>'."\n";
/*****************************************************************************/
$varname="contact_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $contact_site=$_POST[$varname];
} else {
    $contact_site=$site[$varname];
}
echo '<div class="form-group"><label for='.$varname.'>Contact Info:</label>'
     . '<textarea '
     . 'name="'.$varname.'" rows="2">'
     . $contact_site.'</textarea> <br/>Information about how to contact the
     site manager. Can include telephone numbers and/or email addresses.</div>'."\n";
/*****************************************************************************/
$varname="street_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $street_site=$_POST[$varname];
} else {
    $street_site=$site[$varname];
}
echo '<div class="form-group"><label for='.$varname.'>Street:</label><input type="text" '
     . 'name="'.$varname.'" maxlength="256" value="'
     . $street_site.'"> <br/>Standard Postal Number and street.</div>'."\n";
/*****************************************************************************/
$varname="city_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $city_site=$_POST[$varname];
} else {
    $city_site=$site[$varname];
}
echo '<div class="form-group"><label for='.$varname.'>City:</label><input type="text" '
     . 'name="'.$varname.'" maxlength="256" value="'
     . $city_site.'"> <br/>Standard Postal Address City.</div>'."\n";

/*****************************************************************************/
$varname="state_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $state_site=$_POST[$varname];
} else {
    $state_site=$site[$varname];
}
echo '<div class="form-group"><label for='.$varname.'>State<br>(abbreviated):</label><input type="text" '
     . 'name="'.$varname.'" size="2" maxlength="2" value="'
     . $state_site.'"> <br/>State, using 2 letters and no punctuation.</div>'."\n";
/*****************************************************************************/
$varname="zip_site";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $zip_site=$_POST[$varname];
} else {
    $zip_site=$site[$varname];
}
echo '<div class="form-group"><label for='.$varname.'>Zip Code:</label><input type="text" '
     . 'name="'.$varname.'" size="5" maxlength="5" value="'
     . $zip_site.'"> <br/>5 digit zip code.</div>'."\n";
/*****************************************************************************/
$varname="lat_site";
if (!isset($lat_site)) {
  if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
      $lat_site=$_POST[$varname];
  } else {
      $lat_site=$site[$varname];
  }
}
echo '<div class="form-group"><label for='.$varname.'>Latitude:</label><input type="number" step="any" '
     . 'name="'.$varname.'" value="'
     . $lat_site.'">
     <br/>Format: 29.1234567</div>'."\n";


/*****************************************************************************/
$varname="long_site";
if (!isset($long_site)) {
  if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
      $long_site=$_POST[$varname];
  } else {
      $long_site=$site[$varname];
  }
}
echo '<div class="form-group"><label for='.$varname.'>Longitude:</label><input type="number" step="any" '
     . 'name="'.$varname.'"  value="'
     . $long_site.'"> <br/>Format: -90.1234567
     </div>'."\n";

/*****************************************************************************/
$varname="active_site";
if (isset($_POST[$varname])) {
    if ($_POST[$varname]=="Yes") { $active_site=1;}
    else {$active_site=0;}
    //$active_site=$_POST[$varname];
} else {
    $active_site=$site[$varname];
}
echo '<div class="form-group"><label for='.$varname.'>Active?</label><input type="checkbox" '
     . 'name="'.$varname.'" value="Yes"';
if ($active_site>0) { echo ' checked="checked" ';}
echo '> <br/>"Active" means site is available for rental for SCA events.
If site becomes unavailable due to change in management or for other reasons,
uncheck box. Listing will remain in the database in case it becomes available again
later, but will not display in the public list.</div>'."\n";


echo '<input type="submit" value="Update Event Site Information">';
//echo '<button type="reset" value="Reset">Reset</button>';
echo '</form>';

/*****************************************************************************/
/*****************************************************************************/
echo "<p>";
// Add Links back to the main list, and to the next site needing to be verified.
echo "<div class=\"btn-group\" role=\"group\" aria-label=\"navigation\">\n";
if ($previous_item >= 1) {
    echo button_link("./edit_site.php?id=".$previous_item, "Previous Site")."\n";
}
if ($next_item < $max_item) {
    echo button_link("./edit_site.php?id=".$next_item, "Next Site")."\n";
}

$query="SELECT id_site, verified_site from Sites order by verified_site desc, id_site;";
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute query");
if (mysqli_num_rows($result)>=1) {
   $next_site= mysqli_fetch_assoc($result);

    echo button_link("./edit_site.php?id=".$next_site["id_site"],
                     "Next Site Needed to Verify");
}

echo button_link("./list_site.php", "Return to List of Sites")."\n";
echo "</div><!-- ./col-md-8 --></div><!-- ./row -->\n"; //close out list and open divs


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

    /* Testing code
    echo "<p>Query is " . $update . "<p>";
    echo "Value of $active_site is ".$active_site
            ." and from post is ".$_POST["active_site"]
            ." and from query is ".$site["active_site"]."<p>";

    */
    $result=update_query($cxn, $update);
    if ($result !== 1) {
      echo "Error updating record: " . mysqli_error($cxn);
    }
}


mysqli_close ($cxn); /* close the db connection */
?>
