

<?php
/* connect to the database */
//$cxn = mysqli_connect ("localhost", "oop", "ooppassword","oop")
//or die ("message");
$cxn = mysqli_connect (SERVER,USERNAME,PASSWORD,DATABASE)
or die ("message");

// Build links to the list beginning with the appropriate initial, which is returned as $Initial

$Initial = $_GET["initial"];
echo "<div class='page-header'><h1>Names beginning with $Initial</h1></div>"; //Customize the page header
echo "<div class='row'><div class='col-md-2'></div><div class='col-md-8'>";
include "alpha.php"; // includes the A-Z link list
include "warning.php"; // includes the warning text about paper precedence
echo "<div class='list-group'><ul type='none'>"; // make the list pretty with formatting
$query = "select id_person, name_person from Persons where upper(substring(name_person,1,1)) ='$Initial'";
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");

while ($row = mysqli_fetch_assoc($result)) {
//    extract($row);
    $Name = $row['name_person'];
    $ID = $row['id_person'];
    $link = "<li class='list-group-item text-left'><a href='./person.php?id=$ID'>$Name</a></li>";
//    $link = "<li> $Name </li>";
    echo "$link";
}

echo "</ul></div> <!-- ./col-md-8 --><div class='col-md-2'></div></div><!-- ./container-fluid -->"; //close out list and open divs

mysqli_close ($cxn); /* close the db connection */
?>
