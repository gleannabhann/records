<?php
/* REST API page for retrieving a list of combat marshals
 * Retrieves public data from the db and outputs it as JSON
 * To use, call the page in any php document, including the
 * ID for the combat type you're retrieving using curl(); this
 * will allow anyone to store the JSON results as a php array
 * using json_decode(); and will further allow them to
 * display the data in a manner of their choosing. Designed
 * to allow other SCA websites to display current
 * information without needing to manually
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
try {
  if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
    // We got here through an api call 
    // store the combat id in a var
    $id_combat = $_GET["id"];
  } else {
    throw new Exception("Martial Type ID must be Numeric. Instead, I received '" . $_GET['id'] . "'");
  }
} catch (Exception $e) {
  echo json_encode(array($e->getMessage()));
  return false;
}

    // if a group ID was also provided, set that here:
try 
{
  if ((isset($_GET['group'])) && (is_numeric($_GET['group']))) {
    $id_group = $_GET["group"];  // If this is set then only marshals from one group are listed.
  } else if (isset($_GET['group']))
  {
    throw new Exception("Group ID must be Numeric. Instead, I received '" .$_GET['group'] . "'");
  }
} catch (Exception $e) {
  echo json_encode(array($e->getMessage()));
  return false;
} 

// initialize the array
$combat = [];
$col_names = [];
$warrants = [];
$data = [];
$q_warr = "SELECT id_marshal, name_marshal, name_combat FROM Marshals, Combat where "
        . "Marshals.id_combat=Combat.id_combat "
        . "AND Marshals.id_combat=:id_combat";
$data[':id_combat'] = $id_combat;
if (DEBUG) 
{
  echo "<p>Warrants query: $q_warr</p>";
  echo "<p>Data array: ";
  print_r($data);
  echo "</p>";
}

$sth = $cxn->prepare($q_warr);
$sth->execute($data);

// If the combat id does not return any warrants at all
// there's no point in continuing to build a full query
if ($sth->rowCount()<1) {
  echo 'error';
  return false;
}

$qlink = "SELECT concat('<a href=''../public/person.php?id=',PCC.id_person,' ''>',name_person,'</a>') "
            . "as 'SCA Name', name_group as 'SCA Group',";
$qnolink = "SELECT PCC.id_person, name_person "
            . "as 'SCA Name', name_group as 'SCA Group',";

$q_head = "PCC.card_marshal as 'card number', "
        . "PCC.expire_marshal as 'expiry date' ";
$q_body = "FROM
   (SELECT Persons.id_person, id_person_combat_card,  name_person, name_group,
           card_marshal, expire_marshal
    FROM Persons_CombatCards, Persons, Groups
    WHERE Persons_CombatCards.id_person=Persons.id_person
    AND Persons.id_group = Groups.id_group ";
if (isset($id_group)) {
  $q_body = $q_body . " AND Persons.id_group=:id_group ";
  $data[':id_group'] = $id_group;
}
$q_body = $q_body ."AND Persons_CombatCards.expire_marshal >= curdate()
    AND id_combat=:id_combat) AS PCC
           LEFT JOIN
    (SELECT COUNT(*) as num_count, id_person
    FROM Persons_Marshals, Marshals
    WHERE Persons_Marshals.id_marshal=Marshals.id_marshal
    AND Marshals.id_combat=:id_combat
    GROUP BY id_person) AS PCount
    ON PCount.id_person = PCC.id_person ";
    // Now we have to add the individual warrants
    while ($warr =  $sth->fetch()) {
        extract($warr);
        $q_head = $q_head . ", if (PA$id_marshal.id_person IS NULL,'No', 'Yes') as '$name_marshal' ";
        $q_body = $q_body .
                "LEFT JOIN
                   (SELECT id_person
                    FROM Persons_Marshals
                    WHERE Persons_Marshals.id_marshal=$id_marshal) AS PA$id_marshal
                    ON PA$id_marshal.id_person=PCC.id_person ";
    }
$query = $qnolink . $q_head . $q_body . "WHERE num_count is not NULL ORDER BY name_person";
if (DEBUG) 
{ 
  echo "<p>Warrants Query is<br> $query <br>and data array is";
  print_r($data);
  echo "</p>";
}

// This part borrowed from report_showtable, minus ability to download file

$sth = $cxn->prepare($query);
$sth->execute($data);
    

// Setting the combat name
$combat["type_combat"] = $name_combat;

while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
       $warrants[] = [$row];
}

$combat["warrants"] = $warrants;

//print_r($combat);

//output the data as JSON
echo json_encode($combat);


?>
