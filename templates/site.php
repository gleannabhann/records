<div class="container">

<?php

if (is_numeric($_GET["id"]) && $_GET["id"] > 0) {
    $id_site=intval($_GET["id"]);
} elseif ($_GET["id"] == -1) {
    echo "The system does not have any information about this site. "
    . "If you believe you reached this page in error, please report it to the "
    . "webminister.";
    exit_with_footer();
} else {
      echo "Invalid argument supplied. The url for this page should include ?id=, followed by a positive integer.";
      exit_with_footer();
  }
/* header.php and header_main.php connect to the database for us */

$query = "SELECT id_site, name_site, street_site, city_site, state_site, zip_site,"
        . "facilities_site, capacity_site, rates_site, contact_site, "
        . "url_site, lat_site, long_site, area_site, active_site, "
        . "kingdom_level_site, verify_phone_site, verify_web_site, verify_visit_site "
        . "FROM Sites "
        . "WHERE id_site= :id_site";
$data = [':id_site' => $id_site];
if (DEBUG) {
    echo "Site query is:<br>$query<p>";
}
$sth = $cxn->prepare($query);
$sth->execute($data);
$details = $sth->fetch();
$sites = [];
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
$kingdom_level_site = $details["kingdom_level_site"];
if ($details["verify_phone_site"] != null) {
    $verify_phone_site = $details["verify_phone_site"];
} else {
    $verify_phone_site = "Not verified yet.";
}

if ($details["verify_web_site"] != null) {
    $verify_web_site = $details["verify_web_site"];
} else {
    $verify_web_site = "Not verified yet.";
}

if ($details["verify_visit_site"] != null) {
    $verify_visit_site = $details["verify_visit_site"];
} else {
    $verify_visit_site = "Not verified yet.";
}


// check to see if lat/lng are absent, and if so, geocode the city/state and
// temp store into $lat_site and $long_site so you get a general location pin
// on the map
if (($lat_site == null || $long_site == null)) {
    // first check for city, state
    if (isset($city_site)  && isset($state_site)) {
        $addr = $city_site . ", " . $state_site;
    // otherwise, if area has a value, use that
    } elseif (isset($area_site)) {
        $addr = $area_site;
    }
    $coords = geocode($addr);
    // make sure we got a good result
    if ($coords) {
        $lat_site = $coords[0];
        $long_site = $coords[1];
    }
}


    if (($active_site > 0) || (permissions("Sites") >= 3)) {
        //TODO: Indicate if site is inactive
        if ($street_site != null) {
            $address = $street_site . ", " . $city_site . ", " . $state_site . " " . $zip_site;
        } else {
            $address = $area_site;
        }
        if ($facilities_site == null) {
            $facilities_site = "No information on file";
        }
        if ($capacity_site == null) {
            $capacity_site = "No information on file";
        }
        if ($rates_site == null) {
            $rates_site = "No information on file";
        }
        if ($contact_site == null) {
            $contact_site = "No information on file";
        }
        // add a row to the array to hand to JS, only if coords are available
        if ($lat_site && $long_site) {
            $site = [$name_site, $lat_site, $long_site, $url_site, $facilities_site, $capacity_site, $rates_site, $address, $contact_site, $id_site];
            $sites[] = $site;
        }
    } else {
        echo "You do not have permission to view this page.";
        echo "<br/><a href='/'>Go Home</a> or use your 'Back' button to return to the previous page.";
        exit_with_footer();
    }


/*#######################################################################################*/
// This will list all the sites in the database.
// If the user is logged in with the Sites roletype, will also include edit/delete/add buttons
echo "<div class='page-header'><h1>" . $name_site . "</h1>";
echo "<small>$address</small></div>"; //Customize the page header


echo "<div class='row'><div id='map' class='col-md-8 col-md-offset-2'></div></div>"; //map canvas

echo "<div class='row'><div class='col-md-10 col-md-offset-1'><hr/></div></div>";

echo "<div class='row'><div class='col-md-6 col-md-offset-3'>";



  //if the user has the proper permission level, give them an add and edit site links
  if (permissions("Sites") >= 3) {
      echo "<p><strong><a href='./add_site.php'>Add a New Site</a> | <a href=\"./edit_site.php?id=$id_site\">Edit this Site</a></strong> | ";
  };
  echo '<strong><a href="./list_site.php">Return to List</a></strong></p>';

        //warn the user if the site is inactive
  if (!$active_site) {
      echo "<h2>Caution! This site is inactive!</h2>";
  }
  echo "<p class='text-left'><strong>Suitable for Kingdom Level Events: </strong>$kingdom_level_site</p>";
  echo "<p class='text-left'><strong>Last Verified</strong> <br>"
        . "<strong>By phone:</strong> $verify_phone_site <br> "
          . "<strong>Web:</strong> $verify_web_site <br> "
          . "<strong>In person visit:</strong> $verify_visit_site <br>"
          . "</p>";
  if ($url_site !="") {
      echo "<p class='text-left'><strong>Website: </strong><a href=\"$url_site\">$name_site</a></p>";
  }

  //list the available facilities
  echo "<p class='text-left'><strong>Available facilities: </strong>$facilities_site</p>";
  //list the site's capacity
  echo "<p class='text-left'><strong>Capacity: </strong>$capacity_site</p>";
  //list the site's rates
  echo "<p class='text-left'><strong>Rates: </strong>$rates_site</p>";
  //show the description of the site's area
  echo "<p class='text-left'><strong>Area: </strong>$area_site</p>";

  //if the site has a street address set, display the address
  if ($street_site != null) {
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
    } else {
        $errName = false;
    }
    if (!$_POST['email']) {
        $errEmail = "Please enter your email address";
    } else {
        $errEmail = false;
    }
    if (!$_POST['msgBody']) {
        $errMessage = "Please enter information about the discrepancy or error";
    } else {
        $errMessage = false;
    }
    if (!$errName && !$errEmail && !$errMessage) {
        if (mail($to, $subject, $body, $from)) {
            $emailresult = '<div class="alert alert-success">Thank you! We appreciate your feedback.</div>';
        } else {
            $emailresult ='<div class="alert alert-danger">I was unable to send your message. Please try again.</div>';
        }
    } else {
        echo "Error with setting up email.";
    }
}






#######################################################################################*/
/* footer.php closes the db connection for us */

?>
<!-- end of php -->


<?php /*

<div class="row">
<div class='col-sm-12 col-md-8 col-md-offset-2'>
  <?php echo form_title("Report a problem with this record") ?>
  <form class="form" role="form" method="post" action="site.php">
   
    <div class='form-group'>
       <div class="input-group">     
        <span class='input-group-addon' style='width: 80px;'>Name</span><label for="name" class="control-label sr-only">Name:</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" value="<?php if (isset($_POST['msgSubmit'])) {
    echo htmlspecialchars($_POST['name']);
} ?>">
       </div>
  </div>  
  <div class='form-group'>


         <div class="input-group">
         <span class='input-group-addon' style='width: 80px;'>Email</span><label for="email" class="control-label sr-only">Email:</label>
        <input type="text" class="form-control" id="email" name="email" placeholder="example@domain.com" 
               value="<?php if (isset($_POST['msgSubmit'])) { echo htmlspecialchars($_POST['email']); } ?>">

       </div>
  </div>  
  <div class='form-group'>

         <div class="input-group">     
        <span class='input-group-addon' style='width: 80px;'>Subject</span><label for="subject" class="control-label sr-only">Subject:</label>
          <input type="text" class="form-control" id="subject" name="subject"
                 value="<?php echo "Record correction for $name_site (ID $id_site)"; ?>">
      </div>
  </div>  
  <div class='form-group'>

         <div class="input-group">
        <span class='input-group-addon' style='width: 80px;'>Details</span>
        <label for="msgBody" class="control-label sr-only">Details:</label>
        <textarea cols="62" class="form form-control" rows="4" name="msgBody" placeholder="Tell us what's incorrect about this record." id="msgBody" 
                  value="<?php if (isset($_POST['msgSubmit'])) { echo htmlspecialchars($_POST['msgBody']); } ?>">
        </textarea>

       </div>
  </div>  
  <div class='form-group'>

         <div class="input-group">
          <input id="msgSubmit" name="msgSubmit" type="submit" value="Send Report">
        <input type="hidden" name="id"
             value="<?php
                         echo "$id_site";
                    ?>">
         </div>
  </div>  
  <div class='form-group'>

         <div class="input-group">
        <?php if (isset($emailresult)) {
                        echo $emailresult;
                    } ?>
      </div>
     </div>
</div>
  </form>
</div>

 */ 
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
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php if (defined("MAPSAPI")) { echo constant("MAPSAPI");}?>&callback=initMap">
</script>
</div>
