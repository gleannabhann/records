<?php
/* REST API page for retrieving a person's awards
 * Retrieves public data from the db and outputs it as JSON
 * To use, call the page in any php document, including the
 * ID for the person you're retrieving using curl(); this
 * will allow anyone to store the JSON results as a php array
 * using json_decode(); and will further allow them to
 * display the data in a manner of their choosing. Designed
 * to allow baronial websites to display current award
 * information for their members without needing to manually
 * update the page.
 *
 * To add additional API functions, either insert disambiguation
 * testing (to see which $_GET variable is set) or create a separate
 * page for each type of result.
  */

// configuration
require("../includes/config.php");

// establish the db connection
$cxn = open_db_browse();

// store the passed variable to a local variable after error checking
if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
    // We got here through a search link or directly link on person.php
    // echo "Arrived from person.php";
    $id_person = $_GET["id"];
} else {
    echo 'error';
    return false;
}

// initialize the array
$person = [];
$awards = [];

//fetch the person's name
$query = "SELECT name_person, name_group, Groups.id_group "
        . "FROM Persons, Groups "
        . "WHERE Persons.id_person = :id_person "
        . "AND Persons.id_group=Groups.id_group";
$data = [':id_person' => $id_person];
try {
  $sth = $cxn->prepare($query);
  $sth->execute($data);
} catch (PDOException $e) {
  $error = ['message' => 'Could not complete the query'];
  echo json_encode($error);
  exit;
}

$row = $sth->fetch();
$person["person"] = $row;

/* query: select a person's awards in the database  */
$query = "SELECT  Awards.id_award, name_award, date_award,name_kingdom, name_event, Events.id_event
          FROM Persons, Persons_Awards, Awards, Kingdoms, Events
          WHERE Persons.id_person = Persons_Awards.id_person
         AND Persons_Awards.id_award = Awards.id_award
         AND Awards.id_kingdom = Kingdoms.id_kingdom
         AND Persons_Awards.id_event = Events.id_event
         AND Persons.id_person = :id_person order by date_award";
$data = [':id_person' => $id_person];
try {
  $sth = $cxn->prepare($query);
  $sth->execute($data);
} catch (PDOException $e) {
  $error = ['message' => 'Could not complete the query'];
  echo json_encode($error);
  exit;
} 

while ($row = $sth->fetch())
  {
  extract($row);
  $awards[] = $row;

  }


$person["awards"] = $awards;

//output the data as JSON
echo json_encode($person);


?>
