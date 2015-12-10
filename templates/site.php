<div class="container">

<?php

if (is_numeric($_GET["id"]) && $_GET["id"] > 0)
  {
    $id_site=intval($_GET["id"]);
  } else {
    echo "Invalid argument supplied. The url for this page should include ?id=, followed by a positive integer.";
    exit_with_footer();
  }
/* connect to the database */
$cxn = open_db_browse();
$query = "Select * from sites where id_site= $id_site";

$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query. Query was: ". $query);
$details = mysqli_fetch_array($result);
$sites = array();
$id_site = $details["id_site"];
$name_site = $details["name_site"];
$street_site = $details["street_site"];
$city_site = $details["city_site"];
$state_site = $details["state_site"];
$zip_site = $details["zip_site"];
$facilities_site = $details["facilities_site"];
$capacity_site = $details["capacity_site"];
$rates_site = $details["rates_site"];
$contact_site = $details["contact_site"];
$url_site = $details["url_site"];
$lat_site = $details["lat_site"];
$long_site = $details["long_site"];
$area_site = $details["area_site"];
$active_site = $details["active_site"];

// check to see if lat/lng are absent, and if so, geocode the city/state and
// temp store into $lat_site and $long_site so you get a general location pin
// on the map
if (($lat_site == null || $long_site == NULL))
  {
    // first check for city, state
    if  (isset($city_site)  && isset($state_site))
    {
      $addr = $city_site . ", " . $state_site;
      // otherwise, if area has a value, use that
    } else if (isset($area_site))
    {
      $addr = $area_site;
    }
    $coords = geocode($addr);
    // make sure we got a good result
    if ($coords)
    {
      $lat_site = $coords[0];
      $long_site = $coords[1];
    }

  }


    if (($active_site > 0) || (permissions("Sites") >= 3)) {
        //TODO: Indicate if site is inactive
        if ($street_site != NULL)
        {
          $address = $street_site . ", " . $city_site . ", " . $state_site . " " . $zip_site;
        } else {
          $address = $area_site;
        }
        if ($facilities_site == NULL)
        {
          $facilities_site = "No information on file";
        }
        if ($capacity_site == NULL)
        {
          $capacity_site = "No information on file";
        }
        if ($rates_site == NULL)
        {
          $rates_site = "No information on file";
        }
        if ($contact_site == NULL)
        {
          $contact_site = "No information on file";
        }
        // add a row to the array to hand to JS, only if coords are available
        if ($lat_site && $long_site) {

          $site = array($name_site, $lat_site, $long_site, $url_site, $facilities_site, $capacity_site, $rates_site, $address, $contact_site, $id_site);
          $sites[] = $site;

        }

      } else {
              echo "You do not have permission to view this page.";
              echo "<br/><a href='/'>Go Home</a> or use your 'Back' button to return to the previous page.";
              exit_with_footer();

      }


/*#######################################################################################*/
// This will list all the sites in the database.
// If the user is logged in with the Sites rolytype, will also include edit/delete/add buttons
echo "<div class='page-header'><h1>" . $name_site . "</h1>";
echo "<small>$address</small></div>"; //Customize the page header


echo "<div class='row'><div id='map' class='col-md-8 col-md-offset-2'></div></div>"; //map canvas

echo "<div class='row'><div class='col-md-10 col-md-offset-1'><hr/></div></div>";

echo "<div class='row'><div class='col-md-6 col-md-offset-3'>";



  //if the user has the proper permission level, give them an add and edit site links
  if (permissions("Sites") >= 3){
      echo "<p><strong><a href='./add_site.php'>Add a New Site</a> | <a href=\"./edit_site.php?id=$id_site\">Edit this Site</a></strong>";
  };

        //warn the user if the site is inactive
  if (!$active_site) {

          echo "<h2>Caution! This site is inactive!</h2>";
      }

  if ($url_site !="") echo "<p class='text-left'><strong>Website: </strong><a href=\"$url_site\">$name_site</a></p>";

  //list the available facilities
  echo "<p class='text-left'><strong>Available facilities: </strong>$facilities_site</p>";
  //list the site's capacity
  echo "<p class='text-left'><strong>Capacity: </strong>$capacity_site</p>";
  //list the site's rates
  echo "<p class='text-left'><strong>Rates: </strong>$rates_site</p>";
  //show the description of the site's area
  echo "<p class='text-left'><strong>Area: </strong>$area_site</p>";

  //if the site has a street address set, display the address
  if ($street_site != NULL)
  {
    echo "<p class='text-left'><strong>Address: </strong>$address</p>";
  } else {
    echo "<p class='danger text-left'><strong>Address: </strong>$address</p>";
  }

  echo "<p class='text-left'><strong>Telephone: </strong>$contact_site</p>";





echo "</div><!-- ./col-md-6 --></div><!-- ./row -->"; //close out list and open divs
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
    if (!name) {name = "unknown"};
    if (!lat) {lat = "unknown"};
    if (!lng) {lng = "unknown"};
    var urlStr = 'Website Not Available';
    if (url) {
      urlStr = '<a href="' + url + '"><strong>Visit ' + name + '\'s Web Site</strong></a><br/>'
    };
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
        '<strong>Phone Number: </strong>' + contact + '<br/>'
         + urlStr + '</p></div></div>';

    var marker = new google.maps.Marker({
      position: new google.maps.LatLng(lat, lng),
      title: name,
      map: map,
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
