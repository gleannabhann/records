<div class="container">
<?php

/* connect to the database */
$cxn = open_db_browse();
/*#######################################################################################*/
// This will list all the sites in the database.
// If the user is logged in with the Sites rolytype, will also include edit/delete/add buttons
echo "<div class='page-header'><h1>Sites Sorted by State</h1>";
echo "<small></small></div>"; //Customize the page header

//echo "is_logged_in() returns ". is_logged_in()."<br>";
//echo "permissions(Sites) returns " . permissions("Sites")."<br>";

$query = "select @rn:=@rn+1 as row_number, s.* from Sites s, (SELECT @rn:=0) r order by active_site desc;";
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
echo "<div class='row'><div class='col-md-10 col-md-offset-1'>";

  if (permissions("Sites") >= 3){
      echo "<p>".button_link("./add_site.php", "Add a New Site")."</p>";
  };
echo "<table class='table table-bordered'>
<thead>
<td class='text-left'><strong>Site</strong></td>
<td class='text-left'><strong>Name</strong></td>
<td class='text-left'><strong>Facilities</strong></td>
<td class='text-left'><strong>Capacity</strong></td>
<td class='text-left'><strong>Cost</strong></td>
<td class='text-left'><strong>Area</strong></td>
<td class='text-left'><strong>Address</strong></td>
<td class='text-left'><strong>Contact</strong></td>";
// TODO: replace is_logged_in() with is_site_admin() permissions check
if (permissions("Sites") >= 3){
  echo  "<td class='text-left' style=\"width:5%\"><strong> </strong></td>";
};
echo " </thead>";

while ($row = mysqli_fetch_assoc($result)) {
    extract($row);
    if (($active_site > 0) || (permissions("Sites") >= 3)) {
        //TODO: Indicate if site is inactive
        if ($street_site != NULL)
        {
          $address = $street_site . "<br/>" . $city_site . ", " . $state_site . " " . $zip_site;
        } else {
          $address = "Address Not on File";
        }

        echo "<tr>";
        echo "<td class='text-left' style=\"width:5%\"> $row_number</td>";
        if ($active_site) {
                echo "<td class='text-left' style=\"width:15%\">$name_site";}
            else {
                echo "<td class='text-left' style=\"width:15%\">$name_site (INACTIVE)";
            }
        if ($url_site !="") echo "<a href=\"$url_site\">Link</a>";
        echo "</td>";
        echo "<td class='text-left'>$facilities_site</td>";
        echo "<td class='text-left'> $capacity_site</td>";
        echo "<td class='text-left'>$rates_site</td>";
        echo "<td class='text-left'>$area_site</td>";
        if ($street_site != NULL)
        {
          echo "<td class='text-left'>$address</td>";
        } else {
          echo "<td class='danger text-left'>$address</td>";
        }

        echo "<td class='text-left'>$contact_site</td>";
        if (permissions("Sites") >= 3){
            echo "<td class='text-left'>";
            echo button_link("./edit_site.php?id=".$id_site,"Edit");
            echo "</td>";
        };
        echo "</tr>";
    }
}

echo "</table>";
echo "</div><!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs
echo "<hr><p>Browse by Name:</p><p>";#######################################################################################*/
mysqli_close ($cxn); /* close the db connection */
?>
</div>
