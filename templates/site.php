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
$query = "SELECT id_site, name_site, street_site, city_site, state_site, zip_site,"
        . "facilities_site, capacity_site, rates_site, contact_site, "
        . "url_site, lat_site, long_site, area_site, active_site "
        . "FROM Sites "
        . "WHERE id_site= $id_site";

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


// If the submit button was pressed, handle the email.
if (isset($_POST["msgSubmit"])) {
    //TODO: Need to filter these fields carefully.
  $name = $_POST['name'];
  $email = $_POST['email'];
  $msgBody = wordwrap($_POST['msgBody']);
  $from = 'forms@oopgleannabhann.net';
  $to = 'webminister@gleannabhann.net';
//  $to = 'webminister@gleannabhann.net' . ', ';
//  $to .= 'obsidian@gleannabhann.net';
  $subject = $_POST['subject'];
  $body = "From: $name\n Email: $email\n Message:\n $msgBody";

  // check for name
  if (!$_POST['name']) {
    $errName = "Please enter your name";
  } else {$errName = false;}
  if (!$_POST['email']) {
    $errEmail = "Please enter your email address";
  } else {$errEmail = false;}
  if (!$_POST['msgBody']) {
    $errMessage = "Please enter information about the discrepancy or error";
  } else {$errMessage = false;}
  if (!$errName && !$errEmail && !$errMessage) {
     if (mail ($to, $subject, $body, $from)) {
       $emailresult = '<div class="alert alert-success">Thank you! We appreciate your feedback.</div>';
     } else {
       $emailresult ='<div class="alert alert-danger">I was unable to send your message. Please try again.</div>';
     }
   } else {
       echo "Error with setting up email.";
   }
}






#######################################################################################*/
mysqli_close ($cxn); /* close the db connection */

?>
<!-- end of php -->
<div class="row">
  <?php echo form_title("Report a problem with this record") ?>
  <form class="form-horizontal" role="form" method="post" action="site.php">
    <div class="form-group">
      <label for="name" class="col-sm-2 col-md-3 control-label">Name:</label>
      <div class="input-group col-sm-10 col-md-6">
        <span style="display: block; width: 100%"><input size="60" type="text" width="100%" class="form-control" id="name" name="name" placeholder="Your Name" value="<?php if (isset($_POST['msgSubmit'])) {echo htmlspecialchars($_POST['name']);} ?>"></span>
      </div>
    </div>
    <div class="form-group">
      <label for="email" class="col-sm-2 col-md-3 control-label">Email:</label>
      <div class="input-group col-sm-10 col-md-6">
  <input size="60" type="text" width="100%" class="form-control" id="email" name="email" placeholder="example@domain.com" value="<?php if (isset($_POST['msgSubmit'])) {echo htmlspecialchars($_POST['email']);} ?>">

      </div>
    </div>
    <div class="form-group">
      <label for="subject" class="col-sm-2 col-md-3 control-label">Subject:</label>
      <div class="input-group col-sm-10 col-md-6">
        <input size="60" type="text" class="form-control" id="subject" name="subject"
             value="<?php
                         echo "Record correction for $name_site (ID $id_site)";
                    ?>">
      </div>
    </div>
    <div class="form-group">
      <label for="msgBody" class="col-sm-2 col-md-3 control-label">Details:</label>
      <div class="input-group col-sm-10 col-md-6">
          <textarea cols="62" class="form form-control" rows="4" name="msgBody" placeholder="Tell us what's incorrect about this record." id="msgBody" value="<?php if (isset($_POST['msgSubmit'])) {echo htmlspecialchars($_POST['msgBody']);} ?>">
        </textarea>

      </div>
    </div>

    <div class="form-group">
      <div class="input-group col-sm-10 col-sm-offset-2 col-md-6 col-md-offset-3">
          <input id="msgSubmit" name="msgSubmit" type="submit" value="Send Report">
        <input type="hidden" name="id"
             value="<?php
                         echo "$id_site";
                    ?>">
         </div>
    </div>
    <div class="form-group">
      <div class="input-group col-sm-10 col-sm-offset-2 col-md-6 col-md-offset-3">
        <?php if(isset($emailresult)) {echo $emailresult;} ?>
      </div>
    </div>
  </form>
</div>

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
var siteData = <?php echo json_encode($sites) ?>;

var mylat = siteData[0][1];
var mylng = siteData[0][2];



function initMap() {

  map = new google.maps.Map(document.getElementById('map'), {
  zoom: 8
  });
}
var map;


function populate() {



    /* store all siteData into individual variables for ease of use
    $name_site = 0, $lat_site = 1, $long_site = 2, $url_site = 3,
    $facilities_site = 4, $capacity_site = 5, $rates_site = 6, $address = 7,
    $contact_site = 8, $id_site = 9 */

    var name = siteData[0][0];

    var url = siteData[0][3];
    var facilities = siteData[0][4];
    var capacity = siteData[0][5];
    var rates = siteData[0][6];
    var address = siteData[0][7];
    var contact = siteData[0][8];
    var id = siteData[0][9];
    if (!name) {name = "unknown"};
    if (!mylat) {mylat = "unknown"};
    if (!mylng) {mylng = "unknown"};
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

         map.setCenter(new google.maps.LatLng(mylat, mylng));
    var marker = new google.maps.Marker({
      position: new google.maps.LatLng(mylat, mylng),
      title: name,
      map: map,
    });

    attachContent(marker, contentString);


}



addLoadEvent(initMap);
addLoadEvent(populate);
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDob9AuNmEVae3K6YFLgKzNMdHX8Q-rojc&callback=initMap">
</script>
</div>
