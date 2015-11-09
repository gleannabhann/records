<div class="container">
<?php
/* connect to the database */
$cxn = open_db_browse();
/*#######################################################################################*/
// This will list all the sites in the database.  
// If the user is logged in with the Sites rolytype, will also include edit/delete/add buttons
echo "<div class='page-header'><h1>Sites Sorted by State</h1><small>";
echo "</small></div>"; //Customize the page header

$query = "select @rn:=@rn+1 as row_number, s.* from Sites s, (SELECT @rn:=0) r;";
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
echo "
<div class='row'>

  <div class='col-md-8 col-md-offset-2'>";
echo "<table class='table table-condensed table-bordered'>
<thead>
<td class='text-left' style=\"width:5%\"><strong>Site</strong></td>
<td class='text-left' style=\"width:15%\"><strong>Name</strong></td>
<td class='text-left' style=\"width:25%\"><strong>Facilities</strong></td>
<td class='text-left' style=\"width:5%\"><strong>Capacity</strong></td>
<td class='text-left' style=\"width:15%\"><strong>Cost</strong></td>
<td class='text-left' style=\"width:20%\"><strong>Area</strong></td>
<td class='text-left' style=\"width:10%\"><strong>Contact</strong></td>";
// TODO: replace is_logged_in() with is_site_admin() permissions check
if (is_logged_in()){
    "<td class='text-left' style=\"width:5%\"><strong></strong></td>";
};
echo " </thead>";

while ($row = mysqli_fetch_assoc($result)) {
    extract($row);
    echo "<tr>
    <td class='text-left' style=\"width:5%\"> $row_number</td>
    <td class='text-left' style=\"width:15%\">$name_site</td>
    <td class='text-left' style=\"width:25%\">$facilities_site</td>
    <td class='text-left' style=\"width:5%\"> $capacity_site</td>
    <td class='text-left' style=\"width:15%\">$rates_site</td>
    <td class='text-left' style=\"width:20%\">$area_site</td>
    <td class='text-left' style=\"width:10%\">$contact_site</td>";
    if (is_logged_in()){
        echo "<td class='text-left' style=\"width:5%\">
        <a href=\"\">Edit</a> &nbsp <a href=\"\">Delete</a>
        </td>";
    };
    echo "</tr>";
}

echo "</table>";
echo "</div><!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs
echo "<hr><p>Browse by Name:</p><p>";#######################################################################################*/
mysqli_close ($cxn); /* close the db connection */
?>
</div>

