<?php
/* This page assumes that it is called with two parameters:
   - name - partial name that we will search on
   - k_id - restricts the kingdom to be searched (k_id=-1 means search all entries)
*/
/* connect to the database */
//$cxn = mysqli_connect ("localhost", "oop", "ooppassword","oop")
//or die ("message");
include "warning.php"; // includes the warning text about paper precedence
$cxn = mysqli_connect (SERVER,USERNAME,PASSWORD,DATABASE)
or die ("message");



// Build links to the list beginning with the appropriate initial, which is returned as $Initial

$part_name = $_GET["name"];
if (ISSET($_GET["k_id"])) {
   $k_id = $_GET["k_id"];
} else {
   $k_id = 13;
//   $k_id = $HOST_KINGDOM_ID;
}




echo "<h2>People matching the search parameters</h2>";
echo "<div class='list-group'><ul type='none'>"; // make the list pretty with formatting

if ($k_id == -1){
  $query = "SELECT id_person, name_person, name_group FROM Persons, Groups
            WHERE Persons.id_group = Groups.id_group
            AND name_person like '%$part_name%'";
          }
else {
  $query = "SELECT id_person, name_person, name_group FROM Persons, Groups
            WHERE Persons.id_group = Groups.id_group
            AND Groups.id_kingdom = $k_id
            AND name_person like '%$part_name%'";
      };
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");

while ($row = mysqli_fetch_assoc($result)) {
//    extract($row);
    $Name = $row['name_person'];
    $ID = $row['id_person'];
    $Group = $row['name_group'];
    $link = "<li class='list-group-item text-left'><a href='./person.php?id=$ID'>$Name</a>&nbsp-&nbsp$Group</li>";
//    $link = "<li> $Name </li>";
    echo "$link";

}
echo "</ul></div> <!-- ./col-md-8 --><div class='col-md-2'></div></div><!-- ./container-fluid -->"; //close out list and open divs

echo "</br>";

echo "<h2>Awards matching the search parameters</h2>";

echo "<div class='list-group'><ul type='none'>"; // make the list pretty with formatting
if ($k_id == -1)
{
  $query = "SELECT id_award, name_award FROM Awards
            WHERE name_award like '%$part_name%'";
}
else {
$query = "SELECT id_award, name_award FROM Awards
          WHERE name_award like '%$part_name%'
          AND id_kingdom = $k_id ";
      };
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");

while ($row = mysqli_fetch_assoc($result)) {
//    extract($row);
    $Name = $row['name_award'];
    $ID = $row['id_award'];
    $link = "<li class='list-group-item text-left'><a href='./list.php?award=$ID'>$Name</a></li>";
//    $link = "<li> $Name </li>";
    echo "$link";
}

echo "</ul></div> <!-- ./col-md-8 --><div class='col-md-2'></div></div><!-- ./container-fluid -->"; //close out list and open divs

mysqli_close ($cxn); /* close the db connection */
?>
