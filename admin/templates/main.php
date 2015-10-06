<div>
  <div class="jumbotron">
  <h1>Search</h1>
  <p>Search the database here, or choose a letter below</p>
  <p><form class="form" role="search" action="search.php" method="post">
    <div class="form-group">
      <input type="text" class="form-control" placeholder="Search for Name or Award" name="name">
    </div>
    <button type="submit" class="btn btn-default">Submit</button>
  </form></p>



Click on an initial to see all persons whose name begin with that letter.

<?php
/* connect to the database */
//$cxn = mysqli_connect ("localhost", "oop", "ooppassword","oop")
//or die ("message");
$cxn = mysqli_connect (SERVER,USERNAME,PASSWORD,DATABASE)
or die ("message");

include "alpha.php";


echo "</br>";
mysqli_close ($cxn); /* close the db connection */
?>
</div>
</div>
