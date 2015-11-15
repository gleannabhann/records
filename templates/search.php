<div class="container">
<?php
/* This page assumes that it is called with two parameters:
   - name - partial name that we will search on
   - k_id - restricts the kingdom to be searched (k_id=-1 means search all entries)
*/
/* connect to the database */
$cxn = open_db_browse();
// Build links to the list beginning with the appropriate initial, which is returned as $Initial
$part_name = $_GET["name"];
if (ISSET($_GET["k_id"])) {
   $k_id = $_GET["k_id"];
} else {
//   $k_id = 13;
//   $k_id = HOST_KINGDOM_ID;
   $k_id=-1; 
}
echo "<div class='page-header'><h1>Search results for <i>$part_name</i></h1><small>";
include "warning.php"; // includes the warning text about paper precedence
echo "</small></div>"; //Customize the page header
echo "<div class='container'>";
echo "(<small><a href='#awards'>Skip to awards</a></small>)</br>";
echo "(<small><a href='#groups'>Skip to groups</a></small>)";
echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
/*#######################################################################################*/
echo "<h2>People matching <i>$part_name</i></h2>";
echo "<div class='list-group'><ul type='none'>"; // make the list pretty with formatting
if ($k_id == -1){
  $query = "SELECT id_person, name_person, name_group FROM Persons, Groups
            WHERE Persons.id_group = Groups.id_group
            AND name_person like '%$part_name%' "
          . "ORDER BY name_person";
          }
else {
  $query = "SELECT id_person, name_person, name_group FROM Persons, Groups
            WHERE Persons.id_group = Groups.id_group
            AND Groups.id_kingdom = $k_id
            AND name_person like '%$part_name%' "
          . "ORDER BY name_person";
      };
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
$matches = $result->num_rows;
echo "$matches people matches";
while ($row = mysqli_fetch_assoc($result)) {
//    extract($row);
    $Name = $row['name_person'];
    $ID = $row['id_person'];
    $Group = $row['name_group'];
    if (is_logged_in()){
        $link = "<li class='list-group-item text-left'><a href='./edit_person.php?id=$ID'>$Name</a>&nbsp-&nbsp$Group</li>";
    } else {
        $link = "<li class='list-group-item text-left'><a href='./person.php?id=$ID'>$Name</a>&nbsp-&nbsp$Group</li>";
    }
//    $link = "<li> $Name </li>";
    echo "$link";
}
echo "</ul></div> <!-- ./col-md-8 --></div><!-- ./row --></div><!-- ./container-->"; //close out list and open divs
/*#######################################################################################*/
echo "<a name='awards'></a><div class='container'><div class='row'><div class='col-md-8 col-md-offset-2'>";
echo "<h2>Awards matching <i>$part_name</i></h2>";
echo "<div class='list-group'><ul type='none'>"; // make the list pretty with formatting
if ($k_id == -1)
{
  $query = "SELECT id_award, name_award FROM Awards
            WHERE name_award like '%$part_name%'"
          . "ORDER BY name_award";
}
else {
$query = "SELECT id_award, name_award FROM Awards
          WHERE name_award like '%$part_name%'
          AND id_kingdom = $k_id "
        . "ORDER BY name_award";
      };
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
$matches = $result->num_rows;
echo "$matches award matches";
while ($row = mysqli_fetch_assoc($result)) {
//    extract($row);
    $Name = $row['name_award'];
    $ID = $row['id_award'];
    $link = "<li class='list-group-item text-left'><a href='./list.php?award=$ID'>$Name</a></li>";
//    $link = "<li> $Name </li>";
    echo "$link";
}
echo "</ul></div> <!-- ./col-md-8 --></div><!-- ./row --></div><!-- ./container-->"; //close out list and open divs
/*#######################################################################################*/
echo "<a name='groups'></a><div class='container'><div class='row'><div class='col-md-8 col-md-offset-2'>";
echo "<h2>Groups matching <i>$part_name</i></h2>";
echo "<div class='list-group'><ul type='none'>"; // make the list pretty with formatting
if ($k_id == -1)
{
  $query = "SELECT id_group, name_group, name_kingdom FROM Groups, Kingdoms
            WHERE name_group like '%$part_name%'
            AND Groups.id_kingdom = Kingdoms.id_kingdom "
          . "ORDER BY name_group";
}
else {
  $query = "SELECT id_group, name_group, name_kingdom FROM Groups, Kingdoms
            WHERE name_group like '%$part_name%'
            AND Groups.id_kingdom = Kingdoms.id_kingdom
            AND Groups.id_kingdom = $k_id "
          . "ORDER BY name_group";
      };
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
$matches = $result->num_rows;
echo "$matches award matches";
while ($row = mysqli_fetch_assoc($result)) {
//    extract($row);
    $Name = $row['name_group'];
    $ID = $row['id_group'];
    $KName = $row['name_kingdom'];
    $link = "<li class='list-group-item text-left'><a href='./list.php?group=$ID'>$Name - $KName</a></li>";
//    $link = "<li> $Name </li>";
    echo "$link";
}
echo "</ul></div> <!-- ./col-md-8 --></div><!-- ./row --></div><!-- ./container-->"; //close out list and open divs
/*#######################################################################################*/
mysqli_close ($cxn); /* close the db connection */
?>
</div>


