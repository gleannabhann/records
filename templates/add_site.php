<?php
// Purpose: to enter data for a new site,
// Privileges needed: permissions("Sites")>= 3

if (permissions("Sites")>=  3) {
// If we got here from Post: 
//    - add the new site and include a message
//    - reset the form?
   if ($_SERVER['REQUEST_METHOD'] ==  'POST') {
      // Build the update query
       
       $query_head = "INSERT INTO Sites (name_site";
       $query_tail = " VALUES (";
       
       $name_site =   sanitize_mysql($_POST["name_site"]);
       $query_tail = $query_tail."'$name_site'";
       
       $varname = "url_site";
       if (isset($_POST[$varname]) && !empty($_POST[$varname]) && is_string($_POST[$varname])) {
            $url_site =   sanitize_mysql($_POST[$varname]);
            $query_head = $query_head.",$varname";
            $query_tail = $query_tail.",'$url_site'";
       }
       
       $varname = "facilities_site";
       if (isset($_POST[$varname]) && !empty($_POST[$varname]) && is_string($_POST[$varname])) {
            $facilities_site = sanitize_mysql($_POST[$varname]);
            $query_head = $query_head.",$varname";
            $query_tail = $query_tail.",'$facilities_site'";
       }
       
       $varname = "capacity_site";
       if (isset($_POST[$varname]) && !empty($_POST[$varname]) && is_numeric($_POST[$varname])) {
            $capacity_site = $_POST[$varname];
            $query_head = $query_head.",$varname";
            $query_tail = $query_tail.",$capacity_site";
       }
       
       $varname = "rates_site";
       if (isset($_POST[$varname]) && !empty($_POST[$varname]) && is_string($_POST[$varname])) {
            $rates_site =   sanitize_mysql($_POST[$varname]);
            $query_head = $query_head.",$varname";
            $query_tail = $query_tail.",'$rates_site'";
       }

       $varname = "contact_site";
       if (isset($_POST[$varname]) && !empty($_POST[$varname]) && is_string($_POST[$varname])) {
            $contact_site =   sanitize_mysql($_POST[$varname]);
            $query_head = $query_head.",$varname";
            $query_tail = $query_tail.",'$contact_site'";
       }

       $varname = "lat_site";
       if (isset($_POST[$varname]) && !empty($_POST[$varname]) && is_numeric($_POST[$varname])) {
            $lat_site = $_POST[$varname];
            $query_head = $query_head.",$varname";
            $query_tail = $query_tail.",$lat_site";
       }
       
       $varname = "long_site";
       if (isset($_POST[$varname]) && !empty($_POST[$varname]) && is_numeric($_POST[$varname])) {
            $long_site = $_POST[$varname];
            $query_head = $query_head.",$varname";
            $query_tail = $query_tail.",$long_site";
       }
       
       $varname = "street_site";
       if (isset($_POST[$varname]) && !empty($_POST[$varname]) && is_string($_POST[$varname])) {
            $street_site =   sanitize_mysql($_POST[$varname]);
            $query_head = $query_head.",$varname";
            $query_tail = $query_tail.",'$street_site'";
       }

       $varname = "city_site";
       if (isset($_POST[$varname]) && !empty($_POST[$varname]) && is_string($_POST[$varname])) {
            $city_site =   sanitize_mysql($_POST[$varname]);
            $query_head = $query_head.",$varname";
            $query_tail = $query_tail.",'$city_site'";
       }

       $varname = "state_site";
       if (isset($_POST[$varname]) && !empty($_POST[$varname]) && is_string($_POST[$varname])) {
            $state_site =   sanitize_mysql($_POST[$varname]);
            $query_head = $query_head.",$varname";
            $query_tail = $query_tail.",'$state_site'";
       }

       $varname = "zip_site";
       if (isset($_POST[$varname]) && !empty($_POST[$varname]) && is_string($_POST[$varname])) {
            $zip_site =   sanitize_mysql($_POST[$varname]);
            $query_head = $query_head.",$varname";
            $query_tail = $query_tail.",'$zip_site'";
       }

//       $varname = "area_site";
//       if (isset($_POST[$varname]) && !empty($_POST[$varname]) && is_string($_POST[$varname])) {
//            $url_site =   sanitize_mysql($_POST[$varname]);
//            $query_head = $query_head.",$varname";
//            $query_tail = $query_tail.",'$area_site'";
//       }

       $var_name="area_site";
       //$area_site = "$street_site, $city_site, $state_site, $zip_site";
       $area_site="";
       if (!empty($street_site)) { $area_site="$street_site";}
       if (!empty($city_site)) {
           if (!empty($area_site)) { $area_site=$area_site.", $city_site";
           } else { $area_site = $city_site; }
       }
       if (!empty($state_site)) {
           if (!empty($area_site)) { $area_site=$area_site.", $state_site";
           } else { $area_site = $state_site; }
       }
       if (!empty($zip_site)) {
           if (!empty($area_site)) { $area_site=$area_site.", $zip_site";
           } else { $area_site = $zip_site; }
       }
       if (!empty($area_site)){
           $query_head=$query_head.",area_site";
           $query_tail=$query_tail.",'$area_site'";
       }
       
       $query_head=$query_head.",active_site) ";
       if (isset($_POST["active_site"])) {
           $query_tail=$query_tail.",1);";
//           echo "Active site checkbox was checked<br>";
       } else {
           $query_tail=$query_tail.",0);";
//           echo "Active site checkbox was not checked<br>";
       }
       
       $query = $query_head.$query_tail;
       //echo "Query is $query<br>";
       $cxn = open_db_browse();
       $result=update_query($cxn, $query);
       if ($result !== 1) {
           echo "Error updating record: " . mysqli_error($cxn);
       } else {
           echo "Successfully added $name_site to the List of known sites.<br>";
           echo '<a href="./list_site.php">Return to List of Sites</a><br>';
           echo 'Continue adding new sites below:';
       }
       mysqli_close ($cxn); /* close the db connection */

           
   }
} else {
    // We don't have sufficient permissions for this page.
    echo '<p class = "error"> This page has been accessed in error.</p>';
    echo 'Please use your back arrow to return to the previous page.';
    exit_with_footer();
}

// Since the form does not require PHP, we will create it below
?>

<div class='row'><div class='col-md-8 col-md-offset-2'>
<form action="add_site.php" method="post">
  <h2>Adding a New Event Site</h2>
  <table class='table table-condensed table-bordered'>
      <tr>
          <td class="text-right">Name of Site<br>(Required)</td>
          <td><input type="text" name="name_site" size="50" maxlength="256" required>
          </td>
      </tr>
      <tr>
          <td class="text-right">URL of Site</td>
          <td><input type="url" name="url_site" size="50" maxlength="256" ></td>
      </tr>
      <tr>
          <td class="text-right">Facilities</td>
          <td><textarea name="facilities_site" rows="3" cols="50"></textarea></td>
      </tr>
      <tr>
          <td class="text-right">Capacity</td>
          <td><input type="number" name="capacity_site"></td>
      </tr>
      <tr>
          <td class="text-right">Rates</td>
          <td><textarea name="rates_site" rows="3" cols="50"></textarea></td>
      </tr>
<!--      <tr>
          <td class="text-right">Area (Deprecated by address)</td>
          <td><textarea name="area_site" rows="3" cols="50"></textarea></td>
      </tr>
      <tr>-->
          <td class="text-right">Contact</td>
          <td><textarea name="contact_site" rows="3" cols="50"></textarea></td>
      </tr>
      <tr>
          <td class="text-right">Latitude</td>
          <td><input type="number" step="any" name="lat_site"></td>
      </tr>
      <tr>
          <td class="text-right">Longitude</td>
          <td><input type="number" step="any" name="long_site"></td>
      </tr>
      <tr>
          <td class="text-right">Street</td>
          <td><input type="text" name="street_site" size="50" maxlength="256" ></td>
      </tr>
      <tr>
          <td class="text-right">City</td>
          <td><input type="text" name="city_site" size="50" maxlength="256" ></td>
      </tr>
      <tr>
          <td class="text-right">State <br> (Abbreviated)</td>
          <td><input type="text" name="state_site" size="2" maxlength="2" ></td>
      </tr>
      <tr>
          <td class="text-right">Zip</td>
          <td><input type="text" name="zip_site" size="5" maxlength="5" ></td>
      </tr>
      <tr>
          <td class="text-right">Active</td>
          <td><input type="checkbox" name="active_site" value="1" checked="checked"></td>
      </tr>
  </table>
  <input type="submit" value="Add Event Site Information">
</form>  
</div><!-- ./col-md-8 --></div><!-- ./row -->