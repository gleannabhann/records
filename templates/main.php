<div>
  <div class="jumbotron">
  <h1>Welcome <br/><small>to the Glean Abhann Kingdom Heraldry Database!</small> </h1>
<!--
  <p>Search or browse using the bar at the top of the page, or...</p>
  <p><a class="btn btn-primary btn-lg" href="#" role="button">Click here to learn more</a></p>
-->
</div>
<?php
/* connect to the database */
//$cxn = mysqli_connect ("localhost", "oop", "ooppassword","oop")
//or die ("message");
$cxn = mysqli_connect (SERVER,USERNAME,PASSWORD,DATABASE)
or die ("message");

// Build links to the list beginning with the appropriate initial, which is returned as $Initial
$query = "select count(*) as ct, substring(name_person,1,1) as Initial from Persons group by Initial";
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
while ($row = mysqli_fetch_assoc($result)) {
//    extract($row);
    $Initial = $row['Initial'];
    $link = "<a href='./list.php?initial=$Initial'>$Initial</a>&nbsp";
    echo $link;
}


echo "</br>";

/* query: select a person's awards in the database in the db */
<!--
$query = "SELECT name_person, name_award, date_award from Persons, Awards_Persons, Awards
   WHERE Persons.id_person = Awards_Persons.id_person
         and Awards_Persons.id_award = Awards.id_award
         and Persons.id_person = 200 order by date_award";
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
while ($row = mysqli_fetch_assoc($result))
  {extract($row);
  echo "<b>$name_person</b> was awarded <i>$name_award</i> on $date_award. <br/>";
};
-->

mysqli_close ($cxn); /* close the db connection */
?>

</div>
