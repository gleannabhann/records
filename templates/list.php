<div class="container">
<?php
/* connect to the database */
//$cxn = mysqli_connect ("localhost", "oop", "ooppassword","oop")
//or die ("message");
$cxn = mysqli_connect (SERVER,USERNAME,PASSWORD,DATABASE)
or die ("message");
/*#######################################################################################*/
// This section wil list persons beginning with initial if initial is passed
if (isset($_GET["initial"])) {
$Initial = $_GET["initial"];
echo "<div class='page-header'><h1>Names beginning with $Initial</h1><small>";
include "warning.php"; // includes the warning text about paper precedence
echo "</small></div>"; //Customize the page header
echo "<div class='row'><div class='col-md-8 offset-md-2'>";
include "alpha.php"; // includes the A-Z link list
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
echo "</ul></div> <!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs
}
/*#######################################################################################*/
// This section will list persons with a given award if award is passed
if (!isset($_GET["initial"]) && isset($_GET["award"])){
$award = $_GET["award"];
$query = "select name_award from Awards where id_award=$award";
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
$row = mysqli_fetch_assoc($result);
$name_award = $row['name_award'];
echo "<div class='page-header'><h1>Persons who hold the award $name_award</h1></div>"; //Customize the page header
echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
include "alpha.php"; // includes the A-Z link list
include "warning.php"; // includes the warning text about paper precedence
echo "<div class='list-group'><ul type='none'>"; // make the list pretty with formatting
$query = "select Persons.id_person as ip, name_person, date_award from Persons, Awards_Persons where Persons.id_person = Awards_Persons.id_person and Awards_Persons.id_award=$award";
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
while ($row = mysqli_fetch_assoc($result)) {
    $Name = $row['name_person'];
    $ID = $row['ip'];
    $Date_award = $row['date_award'];
    $link = "<li class='list-group-item text-left'><a href='./person.php?id=$ID'>$Name</a> - $Date_award</li>";
    echo "$link";
}
echo "</ul></div> <!-- ./col-md-8 --></div><!-- ./container-fluid -->"; //close out list and open divs
}
/*#######################################################################################*/
// This section will list persons in a given group if group is passed
if (!isset($_GET["initial"]) && !isset($_GET["award"]) && isset($_GET["group"])){
$group = $_GET["group"];
$query = "select name_group from Groups where id_group=$group";
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
$row = mysqli_fetch_assoc($result);
$name_group = $row['name_group'];
echo "<div class='page-header'><h1>Persons who belong to $name_group</h1></div>"; //Customize the page header
echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
include "alpha.php"; // includes the A-Z link list
include "warning.php"; // includes the warning text about paper precedence
echo "<div class='list-group'><ul type='none'>"; // make the list pretty with formatting
$query = "SELECT Persons.id_person as ip, name_person, id_group FROM Persons
          WHERE  Persons.id_group = $group";
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
while ($row = mysqli_fetch_assoc($result)) {
    $Name = $row['name_person'];
    $ID = $row['ip'];
    $link = "<li class='list-group-item text-left'><a href='./person.php?id=$ID'>$Name</a> </li>";
    echo "$link";
}
echo "</ul></div> <!-- ./col-md-8 --></div><!-- ./container-fluid -->"; //close out list and open divs
}
/*#######################################################################################*/
mysqli_close ($cxn); /* close the db connection */
?>
</div>

