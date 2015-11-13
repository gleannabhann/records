<?php
// Purpose: to enter data for a new site,
// Privileges needed: permissions("Sites")>= 3

if (permissions("Sites")>= 3) {
// If we got here from Post: 
//    - add the new site and include a message
//    - reset the form?
   if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Build the update query
   }
} else {
    // We don't have sufficient permissions for this page.
    echo '<p class="error"> This page has been accessed in error.</p>';
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
          <td class="text-right">Name of Site</td>
          <td><input type="text" name="name_site" size="50" maxlength="256" required></td>
      </tr>
      <tr>
          <td class="text-right">URL of Site</td>
          <td><input type="url" name="name_site" size="50" maxlength="256" ></td>
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
      <tr>
          <td class="text-right">Area (Deprecated by address)</td>
          <td><textarea name="area_site" rows="3" cols="50"></textarea></td>
      </tr>
      <tr>
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
          <td><input type="checkbox" name=active_site" value="1" checked="checked"></td>
      </tr>
  </table>
</form>  
</div><!-- ./col-md-8 --></div><!-- ./row -->