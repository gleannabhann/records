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

$Initial = $_GET["initial"];
echo "<p>Listing all people whose name begins with $Initial</p>";
$query = "select id_person, name_person from Persons where upper(substring(name_person,1,1)) ='$Initial'";
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");

while ($row = mysqli_fetch_assoc($result)) {
//    extract($row);
    $Name = $row['name_person'];
    $ID = $row['id_person'];
    $link = "<li><a href='./person.php?id=$ID'>$Name</a></li>";
//    $link = "<li> $Name </li>";
    echo $link;
}

echo "</br>";

mysqli_close ($cxn); /* close the db connection */
?>

</div>
