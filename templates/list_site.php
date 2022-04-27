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



echo "<div class='row'><div class='col-md-10 col-md-offset-1'><hr/>";

  if (permissions("Sites") >= 3){
      echo '<p>'.button_link("./add_site.php", "Add a New Site").'</p>';
  } else {
      echo '<p>'.button_link(
          "./submit_campsite_report.php",
          "Report a New Site"
      ).'</p>';
  }
echo "<table class='table table-bordered'>
<thead>
<td class='text-left'><strong>Site</strong></td>
<td class='text-left'><strong>Name (Click name for more information)</strong></td>
<!-- <td class='text-left'><strong>Facilities</strong></td> -->
<td class='text-left'><strong>Capacity</strong></td>
<!-- <td class='text-left'><strong>Cost</strong></td>
<td class='text-left'><strong>Area</strong></td> -->
<td class='text-left'><strong>Location</strong></td>
<!--<td class='text-left'><strong>Contact</strong></td> -->";
// TODO: replace is_logged_in() with is_site_admin() permissions check
if (permissions("Sites") >= 3){
  echo  "<td class='text-left'><strong> </strong></td>";
};
echo " </thead>";
$sites = [];
if (permissions("Sites") >= 3) {
    $websiteurl = "/public/edit_site.php";
} else {
    $websiteurl = "/public/site.php";
}
while ($row = mysqli_fetch_assoc($result)) {
    extract($row);
    if (($active_site > 0) || (permissions("Sites") >= 3)) {
        //TODO: Indicate if site is inactive
        if ($street_site != NULL)
        {
          $address = $street_site . "<br/>" . $city_site . ", " . $state_site . " " . $zip_site;
        } else {
          $address = "Address Not on File<br/>" . $area_site;
        }
        // add a row to the array to hand to JS, only if coords are available
        if ($lat_site && $long_site) {

          $site = [$name_site, $lat_site, $long_site, $url_site, $facilities_site, $capacity_site, $rates_site, $address, $contact_site, $id_site, $kingdom_level_site];
          $sites[] = $site;

        }

        echo "<tr>";

        echo "<td class='text-left'><a name='$id_site'>$row_number</a></td>";
        if ($active_site) {
            if ($kingdom_level_site == 'Yes') {
                $kle = " (KLE) ";
            } else {
                $kle = " ";
            }
            echo "<td class='text-left'><a href='".$websiteurl."?id=" . $id_site . "'>" . $name_site . "$kle</a></td>";
        } else {
                echo "<td class='text-left'><a href='".$websiteurl."?id=" . $id_site . "'>" . $name_site . "$kle (INACTIVE)</a></td>";
        }
        //if ($url_site !="") echo "<a href=\"$url_site\"> (Website)</a>";
        echo "</td>";
        //echo "<td class='text-left'>$facilities_site</td>";
        echo "<td class='text-left'> $capacity_site</td>";
        //echo "<td class='text-left'>$rates_site</td>";
        //echo "<td class='text-left'>$area_site</td>";
        if ($street_site != NULL)
        {
          echo "<td class='text-left'>$address</td>";
        } else {
          echo "<td class='danger text-left'>$address</td>";
        }

        //echo "<td class='text-left'>$contact_site</td>";
        if (permissions("Sites") >= 3){


            echo "<td class='text-left'>".button_link("./edit_site.php?id=$id_site", "Edit")."</td>";
        };
        echo "</tr>";
    }
}

echo "</table>";
echo "</div><!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs
#######################################################################################*/
mysqli_close ($cxn); /* close the db connection */

?>

<script>
var oldWindow;
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

function attachContent(marker, content) {

  var infowindow = new google.maps.InfoWindow({
    content: content
  });


  marker.addListener('click', function() {
    if (oldWindow) {oldWindow.close();}
    infowindow.open(marker.get('map'), marker);
    oldWindow = infowindow;
  });
};

function initMap() {
map = new google.maps.Map(document.getElementById('map'), {
center: {lat: 33.535442, lng: -90.603519},
zoom: 6
});
}
var map;

var siteData = <?php echo json_encode($sites) ?>;
function populate() {


  for (i = 0; i < siteData.length; i++)
  {

    /* store all siteData into individual variables for ease of use
    $name_site = 0, $lat_site = 1, $long_site = 2, $url_site = 3,
    $facilities_site = 4, $capacity_site = 5, $rates_site = 6, $address = 7,
    $contact_site = 8, $id_site = 9 */

    var name = siteData[i][0];
    var lat = siteData[i][1];
    var lng = siteData[i][2];
    var url = siteData[i][3];
    var facilities = siteData[i][4];
    var capacity = siteData[i][5];
    var rates = siteData[i][6];
    var address = siteData[i][7];
    var contact = siteData[i][8];
    var id = siteData[i][9];
    var kle = siteData[i][10];
    console.log(kle);
    var icon = "/img/google-red-36.png";
    if (!name) {name = "unknown"};
    if (!lat) {lat = "unknown"};
    if (!lng) {lng = "unknown"};
    if (kle == "Yes") {icon = "/img/google-green-36.png"}
    var urlStr = "Website Not Available";
    if (url) {
      urlStr = '<a href="' + url + '"><strong>Visit ' + name + '\'s Web Site</strong></a><br/>'
    }
    if (!facilities) {facilities = "unknown"};
    if (!capacity) {capacity = "unknown"};
    if (!rates) {rates = "unknown"};
    if (!address) {address = "unknown"};
    if (!contact) {contact = "unknown"};
    if (!id) {id = "unknown"};

    //store a url string for contentString


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
        '<a href="/public/site.php?id=' + id + '"><strong>Hall of Records Page</strong></a><br/>' +
         urlStr + '</p></div></div>';


    var marker = new google.maps.Marker({
      position: new google.maps.LatLng(lat, lng),
      title: name,
      map: map,
      icon: icon,
    });

    attachContent(marker, contentString);

  }
}



addLoadEvent(initMap);
addLoadEvent(populate);
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDob9AuNmEVae3K6YFLgKzNMdHX8Q-rojc&callback=initMap">
</script>
</div>
