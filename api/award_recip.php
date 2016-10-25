<?php
/* REST API page for retrieving a list of individuals who
 * have received a specified award.
 * Retrieves public data from the db and outputs it as JSON
 * To use, call the page in any php document, including the
 * ID for the award you're retrieving using curl(); this
 * will allow anyone to store the JSON results as a php array
 * using json_decode(); and will further allow them to
 * display the data in a manner of their choosing. Designed
 * to allow baronial websites to display a list of award
 * recipients or order members without needing to update
 * their site.
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
    $id_award = $_GET["id"];
} else {
  echo '<p>ERROR! You didn\'t supply any parameters. Here is what you can do: </p>';
  echo '<p>To fetch a list of award recipients, supply the award\'s system ID number, ';
  echo 'using the following format: <em>/api/award_recip.php?id=n</em>, where <em>n</em> is the award\'s system ID number.</p> ';
    return false;
}

// initialize the array
$persons = array();
$award = array();

//fetch the award's name
$query = "SELECT name_award, name_group, Groups.id_group "
        . "FROM Awards, Groups "
        . "WHERE Awards.id_award = $id_award "
        . "AND Awards.id_group=Groups.id_group";

$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
$row = mysqli_fetch_assoc($result);
$award["award"] = $row;

/* query: select an award's recipients in the database  */
$query = "SELECT  Persons.id_person, name_person, date_award,name_kingdom, name_event, Events.id_event
          FROM Persons, Persons_Awards, Awards, Kingdoms, Events
          WHERE Persons_Awards.id_person = Persons.id_person
         AND Awards.id_award = Persons_Awards.id_award
         AND Awards.id_kingdom = Kingdoms.id_kingdom
         AND Persons_Awards.id_event = Events.id_event
         AND Awards.id_award = $id_award order by date_award";
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute awards query");
while ($row = mysqli_fetch_assoc($result))
  {
  extract($row);
  $persons[] = $row;

  }


$award["persons"] = $persons;

//output the data as JSON
echo json_encode($award);


?>
