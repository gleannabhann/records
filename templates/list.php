<div>

<?php
/* connect to the database */
//$cxn = mysqli_connect ("localhost", "oop", "ooppassword","oop")
//or die ("message");
$cxn = mysqli_connect (SERVER,USERNAME,PASSWORD,DATABASE)
or die ("message");

// Build links to the list beginning with the appropriate initial, which is returned as $Initial

$Initial = $_GET["initial"];
echo "<div class='page-header'><h1>Listing all people whose name begins with $Initial</h1></div>"; //Customize the page header
echo "<div class='col-md-2'></div>
<div class='col-md-8'>";
include "alpha.php";
echo "<div class='list-group'><ul>";
$query = "select id_person, name_person from Persons where upper(substring(name_person,1,1)) ='$Initial'";
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");

while ($row = mysqli_fetch_assoc($result)) {
//    extract($row);
    $Name = $row['name_person'];
    $ID = $row['id_person'];
    $link = "<li class='list-group-item'><a href='./person.php?id=$ID'>$Name</a></li>";
//    $link = "<li> $Name </li>";
    echo "$link";
}

echo "</ul></div> <!-- .//list-group-->
</div> <!-- ./col-md-8 -->
<div class='col-md-2'></div>
</div>";

mysqli_close ($cxn); /* close the db connection */
?>

</div>
