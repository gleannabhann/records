<div class="container">
<?php
/* connect to the database */
$cxn = open_db_browse();
/*#######################################################################################*/
// This will list all the sites in the database.  
// If the user is logged in with the Sites rolytype, will also include edit/delete/add buttons
echo "<div class='page-header'><h1>Sites Sorted by State</h1><small>";
echo "</small></div>"; //Customize the page header

//echo "is_logged_in() returns ". is_logged_in()."<br>";
//echo "permissions(Sites) returns " . permissions("Sites")."<br>";

$query = "select @rn:=@rn+1 as row_number, s.* from Sites s, (SELECT @rn:=0) r order by active_site desc;";
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
if (permissions("Sites") >= 3){
    "<td class='text-left' style=\"width:5%\"><strong></strong></td>";
};
echo " </thead>";

while ($row = mysqli_fetch_assoc($result)) {
    extract($row);
    if (($active_site > 0) || (permissions("Sites") >= 3)) {
        //TODO: Indicate if site is inactive
        echo "<tr>";
        echo "<td class='text-left' style=\"width:5%\"> $row_number</td>";
        if ($active_site) {
                echo "<td class='text-left' style=\"width:15%\">$name_site";}
            else {
                echo "<td class='text-left' style=\"width:15%\">$name_site (INACTIVE)";
            }   
        if ($url_site !="") echo "<a href=\"$url_site\">Link</a>";
        echo "</td>";
        echo "<td class='text-left' style=\"width:25%\">$facilities_site</td>";
        echo "<td class='text-left' style=\"width:5%\"> $capacity_site</td>";
        echo "<td class='text-left' style=\"width:15%\">$rates_site</td>";
        echo "<td class='text-left' style=\"width:20%\">$area_site</td>";
        echo "<td class='text-left' style=\"width:10%\">$contact_site</td>";
        if (permissions("Sites") >= 3){
            echo "<td class='text-left' style=\"width:5%\">
            <a href=\"./edit_site.php?id=$id_site\">Edit</a> &nbsp <a href=\"\">Delete</a>
            </td>";
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

