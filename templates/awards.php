<?php
?>

<h1> Welcome to the Awards Landing Page which is, like, totally not built yet.</h1>
<?php
/* connect to the database */
$cxn = open_db_browse();

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