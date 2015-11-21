<div class="container">

<?php

/* connect to the database */
$cxn = open_db_browse();
$query = "select @rn:=@rn+1 as row_number, s.* from Sites s, (SELECT @rn:=0) r "
        . "order by active_site desc, state_site, name_site;";
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
/*#######################################################################################*/
// This will list all the sites in the database.
// If the user is logged in with the Sites rolytype, will also include edit/delete/add buttons
echo "<div class='page-header'><h1>Campgrounds Listing</h1>";
echo "<small>Sorted by State</small></div>"; //Customize the page header

//echo "is_logged_in() returns ". is_logged_in()."<br>";
//echo "permissions(Sites) returns " . permissions("Sites")."<br>";

echo "<div id='row'><div id='map' class='col-md-10 col-md-offset-1'></div></div>"; //map canvas



echo "<div class='row'><div class='col-md-10 col-md-offset-1'>";

  if (permissions("Sites") >= 3){
      echo "<p><strong><a href='./add_site.php'>Add a New Site</a></strong></p>";
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
  echo  "<td class='text-left'><strong> </strong></td>";
};
echo " </thead>";
$sites = array();
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
        // add a row to the array to hand to JS, only if coords are available
        if ($lat_site && $long_site) {

          $site = array($name_site, $lat_site, $long_site, $url_site, $facilities_site, $capacity_site, $rates_site, $address, $contact_site, $id_site);
          $sites[] = $site;

        }

        echo "<tr>";

        echo "<td class='text-left'><a name='$id_site'> $row_number</a></td>";
        if ($active_site) {
                echo "<td class='text-left'>$name_site";}
            else {
                echo "<td class='text-left'>$name_site (INACTIVE)";
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
            echo "<td class='text-left'>
            <a href=\"./edit_site.php?id=$id_site\">Edit</a>
            </td>";
        };
        echo "</tr>";
    }
}

echo "</table>";
echo "</div><!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs
#######################################################################################*/
mysqli_close ($cxn); /* close the db connection */
var_dump($sites);
?>

<script>
function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      if (oldonload) {
        oldonload();
      }
      func();
    }
  }
}


var map;
function initMap() {
map = new google.maps.Map(document.getElementById('map'), {
center: {lat: 33.535442, lng: -90.603519},
zoom: 6
});
}
markers = Array();
infoWindows = Array();
var siteData = <?php echo '[' . json_encode($sites) . ']' ?>;
function populate() {
for (i = 0; i < siteData[0].length; i++)
{
  console.log ("i = " + i);
  /* store all siteData into individual variables for ease of use
  $name_site = 0, $lat_site = 1, $long_site = 2, $url_site = 3,
  $facilities_site = 4, $capacity_site = 5, $rates_site = 6, $address = 7,
  $contact_site = 8, $id_site = 9 */

    var name = siteData[0][i][0];
    var lat = siteData[0][i][1];
    var lng = siteData[0][i][2];
    var url = siteData[0][i][3];
    var facilities = siteData[0][i][4];
    var capacity = siteData[0][i][5];
    var rates = siteData[0][i][6];
    var address = siteData[0][i][7];
    var contact = siteData[0][i][8];
    var id = siteData[0][i][9];
    if (!name) {name = "unknown"};
    if (!lat) {lat = "unknown"};
    if (!lng) {lng = "unknown"};
    if (!url) {url = "unknown"};
    if (!facilities) {facilities = "unknown"};
    if (!capacity) {capacity = "unknown"};
    if (!rates) {rates = "unknown"};
    if (!address) {address = "unknown"};
    if (!contact) {contact = "unknown"};
    if (!id) {id = "unknown"};

    console.log ("Begin creating marker for " + name);

    var contentString = '<div id="content">'+
          '<div id="siteNotice">'+
          '</div>'+
          '<h1 id="firstHeading" class="firstHeading">' + name +'</h1>'+
          '<p>' +
          '<strong>Facilities available: </strong>' + facilities + '<br/>' +
          '<strong>Capacity: </strong>' + capacity + '<br/>' +
          '<strong>Rates: </strong>' + rates + '<br/>' +
          '<strong>Address: </strong>' + address + '<br/>' +
          '<strong>Phone Number: </strong>' + contact + '<br/>' +
          //The below linked page doesn't exist yet
      /*  '<a href="/site.php?id=' + id + '"><strong>Hall of Records Page</strong></a><br/>' +  */
          // in the mean time, we'll link to an anchor within the page.
          '<a href="#' + id + '"><strong>Go to location in the list</strong></a><br/>' +
          // the next line is disabled until I figure out how to only display if the variable is defined
    /*    '<a href="' + url + '"><strong>Visit this Campground\'s Web Site</strong></a><br/>' + */
          '</p>'
          '</div>'+
          '</div>';

    infowindow = new google.maps.InfoWindow({
      content: contentString
    });

    var marker = new google.maps.Marker({
      position: new google.maps.LatLng(lat, lng),
      title: name,
      map: map,
      infoWindowIndex: i,
    });

    google.maps.event.addListener(marker, 'click',
      function(event)
      {
          infoWindows[this.infoWindowIndex].open(map, this);
      }
    );

    infoWindows.push(infowindow);
    markers.push(marker);
    console.log ("Marker created for " + name);
  }
}

addLoadEvent(initMap);
addLoadEvent(populate);
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDob9AuNmEVae3K6YFLgKzNMdHX8Q-rojc&callback=initMap">
</script>
</div>
