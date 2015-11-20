<div>
  <div class="jumbotron">
  <h1>Welcome <br/><small>to the Gleann Abhann Kingdom Hall of Records!</small> </h1>
<!--
  <p>Search or browse using the bar at the top of the page, or...</p>
  <p><a class="btn btn-primary btn-lg" href="#" role="button">Click here to learn more</a></p>
-->
<div class="row">
  <div class="col-md-4 col-md-offset-1">
<?php
/* connect to the database */
$cxn = open_db_browse();
include "alpha.php";
mysqli_close ($cxn); /* close the db connection */
?>
</div><!-- ./col-md-4 -->
<div class="col-md-2">
<h2 class="text-center"><i>or</i><h2>
</div><!-- ./col-md-2-->
<div class="col-md-4">
  <h3>Type a partial name in the box to search:</h3>
  <form role="search" action="public/search.php" method="get">
                  <input type="text" class="form-control" placeholder="Search for Name or Award" name="name">
                <button type="submit" class="btn btn-default">Submit</button>
              </form>
</div><!-- ./col-md-4 -->
</div><!-- ./row -->
</div><!-- ./jumbotron -->
<div class="row">
        <div class="col-md-6">
          <h2>Awards</h2>
          <p>Search for or browse awards, groups, and awards recipients. See which awards an individual has recieved. See which individuals have received a particular award.</p>
          <p><a class="btn btn-default" href="/public/awards.php" role="button">View Awards &raquo;</a></p>
        </div>
        <!--<div class="col-md-4">
          <h2>Authorizations</h2>
          <p>Search for or browse authorizations. See which authorizations a fighter holds.</p>
          <p><a class="btn btn-default" href="#" role="button">View Authorizations &raquo;</a></p>
       </div>-->
        <div class="col-md-6">
          <h2>Campsites</h2>
          <p>Browse a list of campgrounds within the Kingdom of Gleann Abhann which may be available to rent for Kingdom or Local events. See details about the amenities each campground has to offer.</p>
          <p><a class="btn btn-default" href="/public/list_site.php" role="button">View Campgrounds &raquo;</a></p>
        </div>
      </div>

<div class="row">
  <div class="col-md-8 col-md-offset-2 text-center">


</div> <!-- ./col-md-8 -->
