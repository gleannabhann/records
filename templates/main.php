<div>
  <div class="jumbotron">
  <h1>Welcome <br/><small>to the Gleann Abhann Kingdom Hall of Records!</small> </h1>
<!--
  <p>Search or browse using the bar at the top of the page, or...</p>
  <p><a class="btn btn-primary btn-lg" href="#" role="button">Click here to learn more</a></p>
-->
</div>

<div class="row">
  <div class="col-md-8 col-md-offset-2 text-center">
    Click on an initial to see all persons whose name begin with that letter.

<?php
/* connect to the database */
//$cxn = mysqli_connect ("localhost", "oop", "ooppassword","oop")
//or die ("message");
$cxn = mysqli_connect (SERVER,USERNAME,PASSWORD,DATABASE)
or die ("message");

include "alpha.php";


echo "<br/>";
mysqli_close ($cxn); /* close the db connection */
?>
</div> <!-- ./col-md-8 -->
</div> <!-- ./row -->
<div class="row">
  <div class="col-md-8 col-md-offset-2 text-center">
Type a partial name in the box to search:
<form role="search" action="public/search.php" method="get">
                <input type="text" class="form-control" placeholder="Search for Name or Award" name="name">
              <button type="submit" class="btn btn-default">Submit</button>
            </form>
          </div> <!-- ./col-md-8 -->
</div> <!-- ./row -->
<br/>
