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
$part_name = str_replace("'", "&#039;", $part_name);
if (ISSET($_GET["k_id"])) {
   $k_id = $_GET["k_id"];
} else {
//   $k_id = 13;
//   $k_id = HOST_KINGDOM_ID;
   $k_id=-1;
}
echo "<div class='page-header'><h1><a name='top'>Search results for <i>$part_name</i></a></h1><small>";
include "warning.php"; // includes the warning text about paper precedence
echo "</small></div>"; //Customize the page header
echo "<div class='container'>";
echo "<small><a href='#awards'>Skip to Awards</a></small> | ";
echo "<small><a href='#groups'>Skip to Groups</a></small> | ";
echo "<small><a href='#events'>Skip to Events</a></small>";
echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
/*#######################################################################################*/
echo form_title("People matching <i>$part_name</i>");
if ((permissions("Herald")>=3) || (permissions("Marshal")>=3)) {
    echo button_link("./add_person.php?part_name=".$part_name, "Add A New Person");
}
echo "<div class='list-group'><ul type='none'>"; // make the list pretty with formatting

//TODO convert these to prepared statements
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
if (DEBUG) {
    echo "Person search query is:<br>$query<p>";
}

$sth = $cxn->prepare($query);
$sth->execute();
$matches = $sth->rowCount();
echo "$matches people matches";
if ($matches > 0) {
  $result = $sth->fetchAll() or die ("Couldn't execute query");
  foreach ($result as $row) {
  //    extract($row);
      $Name = $row['name_person'];
      $ID = $row['id_person'];
      $Group = $row['name_group'];
      if ((permissions("Herald")>=3) || (permissions("Marshal")>=3)) {
          $link = "<li class='list-group-item text-left'><a href='./edit_person.php?id=$ID'>$Name</a>&nbsp-&nbsp$Group</li>";
      } else {
          $link = "<li class='list-group-item text-left'><a href='./person.php?id=$ID'>$Name</a>&nbsp-&nbsp$Group</li>";
      }
  //    $link = "<li> $Name </li>";
      echo "$link";
  }
}
echo "</ul></div> <!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs
/*#######################################################################################*/
echo "<div class='container'><div class='row'><div class='col-md-8 col-md-offset-2'>";
echo form_title("<a name='awards'>Awards matching <i>$part_name</i></a><small><a href='#top'> (Return to Top)</a></small>");
if (permissions("Herald")>=3){
    echo button_link("./add_award.php?part_name=".$part_name, "Add A New Award");
}
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
// perform the query
$sth = $cxn->prepare($query);
$sth->execute();
$matches = $sth->rowCount();
echo "$matches award matches";
if ($sth->rowCount() > 0) {
  $result = $sth->fetchAll() or die ("Couldn't execute query");
  $matches = count($result);
  echo "$matches award matches";
  foreach ($result as $row) {
  //    extract($row);
      $Name = $row['name_award'];
      $ID = $row['id_award'];
      if (permissions("Herald")>=3){
          $link = "<li class='list-group-item text-left'>"
                . "<a href='./edit_award.php?id=$ID&name=$part_name'>"
                . "$Name</a></li>";
      } else {
          $link = "<li class='list-group-item text-left'>"
                . "<a href='./list.php?award=$ID'>$Name</a></li>";
      }
  //    $link = "<li> $Name </li>";
      echo "$link";
  }
}
echo "</ul></div> <!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs
/*#######################################################################################*/
echo " <div class='container'><div class='row'><div class='col-md-8 col-md-offset-2'>";
echo form_title("<a name='groups'>Groups matching <i>$part_name</i></a><small><a href='#top'> (Return to Top)</a></small>");
if (permissions("Herald")>=3){
    echo button_link("./add_group.php?part_name=".$part_name, "Add A New Group");
}echo "<div class='list-group'><ul type='none'>"; // make the list pretty with formatting
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
      }

// execute the query
$sth = $cxn->prepare($query);
$sth->execute();
$matches = $sth->rowCount();
echo "$matches group matches";  
if ($matches > 0) {
  $result = $sth->fetchAll() or die ("Couldn't execute query");


  // display the results
  foreach ($result as $row) {
  //    extract($row);
      $Name = $row['name_group'];
      $ID = $row['id_group'];
      $KName = $row['name_kingdom'];
      if (permissions("Herald")>=3){
          $link = "<li class='list-group-item text-left'>"
                  . "<a href='./edit_group.php?id=$ID&name=$part_name'>"
                  . "$Name - $KName </a></li>";
      } else {
          $link = "<li class='list-group-item text-left'>"
                  . "<a href='./list.php?group=$ID'>$Name - $KName</a></li>";
      }
      echo "$link";
  }
}
echo "</ul></div> <!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs
/*#######################################################################################*/
echo "<div class='container'><div class='row'><div class='col-md-8 col-md-offset-2'>";
echo form_title("<a name='events'>Events matching <i>$part_name</i></a><small><a href='#top'> (Return to Top)</a></small>");
if (permissions("Herald")>=3){
    echo button_link("./add_event.php", "Add A New Event");
}
echo "<div class='list-group'><ul type='none'>"; // make the list pretty with formatting
if ($k_id == -1)
{
  $query = "SELECT id_event, name_event, date_event_start, date_event_stop, name_group, name_kingdom
            FROM Events, Groups, Kingdoms
            WHERE name_event like '%$part_name%'
            AND Events.id_group = Groups.id_group
            AND Groups.id_kingdom = Kingdoms.id_kingdom "
          . "ORDER BY name_event";
}
else {
  $query = "SELECT id_event, name_event, date_event_start, date_event_stop, name_group, name_kingdom
            FROM Events, Groups, Kingdoms
            WHERE name_event like '%$part_name%'
            AND Events.id_group = Groups.id_group
            AND Groups.id_kingdom = Kingdoms.id_kingdom "
          . "AND Groups.id_kingdom = $k_id "
          . "ORDER BY name_group";
      };

// execute the query
$sth = $cxn->prepare($query);
$sth->execute();
$matches = $sth->rowCount();
echo "$matches events matches";
if ($matches > 0) {
  // fetch the result
  $result = $sth->fetchAll() or die ("Couldn't execute query");
  // display the results
  foreach ($result as $row) {
      extract($row);
      if (permissions("Herald")>=3){
          $link = "<li class='list-group-item text-left'>"
                  . "<a href='./edit_event.php?id=$id_event'>"
              . "$name_event</a> hosted by $name_group ($name_kingdom) "
              . "$date_event_start -- $date_event_stop"
              . "</li>";
      } else {
          $link = "<li class='list-group-item text-left'>"
              . "<a href='./event.php?id=$id_event'>"
              . "$name_event</a> hosted by $name_group ($name_kingdom) "
              . "$date_event_start -- $date_event_stop"
              . "</li>";
      }
  //    $link = "<li> $Name </li>";
      echo "$link";
  }
}
echo "</ul></div><small><a href='#top'>Return to Top</a></small><!-- ./col-md-8 --></div><!-- ./row --></div><!-- ./container-->"; //close out list and open divs
/*#######################################################################################*/
$cxn = null; /* close the db connection */
?>
</div>
