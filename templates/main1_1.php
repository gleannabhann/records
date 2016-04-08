<div>
  <div class="jumbotron">

<div class="row">
<div class="col-lg-3 col-md-4 col-xs-6">
    <h2 class="text-center">Awards</h2>
    <p class="text-justify"><small>
The Awards section of the site reflects the Order of Precedence of Gleann
Abhann.  It is possible to search for people, groups, awards, or events using
the handy search box and a partial name.  
</small>
      </p>
</div>
<div class="col-lg-3 col-md-4 col-xs-6">
    <h2 class="text-center">Marshals</h2>
    <p class="text-justify"><small>
The Combat section of the site is used by the fighters of Gleann Abhann to find local marshals, check expiry dates on their authorizations, etc.  Currently we are testing this section with the help of the Rapier community.
</small>
      </p>
</div>
<div class="col-lg-3 col-md-4 col-xs-6">
    <h2 class="text-center">Campgrounds</h2>
    <p class="text-justify"><small>
The Campground section lists known campgrounds in Gleann Abhann, including as much data as we have been able to gather.  Currently, we are looking for volunteers to confirm this data since some of it is very out of date.
</small>
      </p>
</div>
<div class="col-lg-3 col-md-4 col-xs-6">
    <h2 class="text-center">About Us</h2>
    <p class="text-justify"><small>
This website was created by a team of volunteers working under the direction of the Webminister's Office.  As always, all mistakes made are owned by the webminister; all credit goes to her team of Rabid Programming Squirrels.
</small>
      </p>
</div>

</div>

</div><!-- ./jumbotron -->
<div class="row">
  <div class="col-md-10 col-md-offset-1">
    <div class="col-md-4">
      <div class="panel panel-default">
        <div class="panel panel-body">
        <h2 class="text-center">Awards</h2>
        <p class="text-justify">Search for or browse awards, groups, events, and award
          recipients.   Type a partial name in the box to search:
        <form role="search" action="public/search.php" method="get">
                <input type="text" class="form-control" placeholder="Search for Name or Award" name="name">
                <button type="submit" class="btn btn-default">Submit</button>
        </form></p>
        <p>See results from recent events. 
           <a class="btn btn-default" href="/public/awards.php" role="button">View Awards &raquo;</a></p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-body">
      <h2 class="text-center">Authorizations</h2>
        <p class="text-justify">
            See lists of active marshals by type of combat
            <a class="btn btn-default" href="#" role="button">View Authorizations &raquo;</a></p>
       <p> Search for a fighter to see their unexpired authorizations and marshal's warrants. 
       <form role="search" action="public/search.php" method="get">
                <input type="text" class="form-control" placeholder="Search for Name or Award" name="name">
                <button type="submit" class="btn btn-default">Submit</button>
        </form></p>
 
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-body">
        <h2 class="text-center">Campsites</h2>
        <p class="text-justify">Browse a list of campgrounds within the Kingdom
        of Gleann Abhann which may be available to rent for Kingdom or Local
        events. See details about the amenities each campground has to offer.</p>
        <p><a class="btn btn-default" href="/public/list_site.php" role="button">View Campgrounds &raquo;</a></p>
      </div>
    </div>
  </div>
</div>
</div>

