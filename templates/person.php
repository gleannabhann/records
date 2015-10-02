
<div class="container">
<?php
/* connect to the database */
//$cxn = mysqli_connect ("localhost", "oop", "ooppassword","oop")
//or die ("message");
$cxn = mysqli_connect (SERVER,USERNAME,PASSWORD,DATABASE)
or die ("message");

/* query: select a person's name for the header */
$id_person = $_GET["id"];
$query = "SELECT name_person from Persons WHERE Persons.id_person = $id_person";
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
while ($row = mysqli_fetch_assoc($result))
  {extract($row);
  echo "<div class='page-header'><h1>$name_person</h1></div>";
};
echo "
<div class='row'>
  <div class='col-md-2'></div>
  <div class='col-md-8'>";
echo "<table class='table table-condensed table-bordered'>
<thead><td class='text-left'><strong>Award</strong></td>
<td class='text-left'><strong>Date</strong></td></thead>";

/* query: select a person's awards in the database in the db */
$id_person = $_GET["id"];
$query = "SELECT name_person, name_award, date_award,name_kingdom from Persons, Awards_Persons, Awards, Kingdoms
   WHERE Persons.id_person = Awards_Persons.id_person
         and Awards_Persons.id_award = Awards.id_award
         and Awards.id_kingdom = Kingdoms.id_kingdom
         and Persons.id_person = $id_person order by date_award";
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
while ($row = mysqli_fetch_assoc($result))
  {extract($row);
// echo "<tr><td class='text-left'>$name_award - $name_kingdom</td><td class='text-left'>$date_award</tr></td>";
  echo "<tr><td class='text-left'>$name_award</td><td class='text-left'>$date_award</tr></td>";
};
echo "</table>";
include "alpha.php"; // includes the A-Z link list
include "warning.php"; // includes the warning text about paper precedence
echo "
  </div><!-- ./col-md-8 -->
  <div class='col-md-2'></div>
</div><!-- ./row -->"; //close out list and open divs

mysqli_close ($cxn); /* close the db connection */
?>
<!-- end of php -->
</div>
