<!doctype html>
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<meta http-equiv="expires" content="0" />
<html lang="en">
<head>
  <title>  </title>
  <link rel="stylesheet" href="/css/auth.css">
</head>
<body>
<?php
// configuration
require("../includes/config.php");

// builds the auth card for an individual. 

// Initialize Variables
$sca_name = NULL;
$mundane_name = NULL;
$expire_marshal = NULL;
$expire_authorize = NULL;
$issue_date = NULL;
$exp_date = NULL;
$authorizations = [];
$warrants = [];
$mem_number = NULL;
$mem_exp = NULL;


/* connect to the database 
 * Normally we would use the connection set by header.php or header_main.php
 * but those documents are not included here
 * */

$cxn = open_db_browse();

	$itl = "<span class=\"italics\">";
	$itlx = "</span>";
	$ctr = "<span class=\"center\">";
	$ctrx = "</span>";
	$id_person = $_GET["id"];
  $id_combat = 1;

// get authorizations
$query_auth="SELECT A.id_auth, A.name_auth, A.id_combat, PA.id_person_auth
FROM Authorizations A LEFT JOIN Persons_Authorizations PA
ON A.id_auth=PA.id_auth AND id_person=:id_person
WHERE A.id_combat=:id_combat";
  $data = [':id_person' => $id_person, ':id_combat' => $id_combat];
  $sth = $cxn->prepare($query_auth);
  $sth->execute($data);  
  while ($row = $sth->fetch(PDO::FETCH_ASSOC)){
      extract($row);
      if (($row["name_auth"] == "Rapier") && ($row["id_person_auth"] != NULL))
        {

            $authorizations[] = "Rapier";
        }
      if (($row["name_auth"] == "Secondary") && ($row["id_person_auth"] != NULL))
        {

            $authorizations[] = "Secondary";
        }
      if (($row["name_auth"] == "Spear") && ($row["id_person_auth"] != NULL))
        {

            $authorizations[] = "Spear";
        }
      if (($row["name_auth"] == "Cut and Thrust") && ($row["id_person_auth"] != NULL))
        {

            $authorizations[] = "Cut and Thrust";
        }
      if (($row["name_auth"] == "Rapier (Y)") && ($row["id_person_auth"] != NULL))
        {

            $authorizations[] = "Rapier (Y)";
        }
      if (($row["name_auth"] == "Rapier Secondary (Y)") && ($row["id_person_auth"] != NULL))
        {

            $authorizations[] = "Rapier Secondary (Y)";
        }
      if (($row["name_auth"] == "Combat Archery") && ($row["id_person_auth"] != NULL))
        {

            $authorizations[] = "Combat Archery";
        }
      if (($row["name_auth"] == "Armored Combat") && ($row["id_person_auth"] != NULL))
        {

            $authorizations[] = "Armored Combat";
        }
      if (($row["name_auth"] == "Siege Engineer") && ($row["id_person_auth"] != NULL))
        {

            $authorizations[] = "Siege Engineer";
        }
      if (($row["name_auth"] == "Fibreglass Spear") && ($row["id_person_auth"] != NULL))
        {

            $authorizations[] = "Fibreglass Spear";
        }
}

// get marshal information

$query_marshal="SELECT M.id_marshal, M.name_marshal, M.id_combat, PM.id_person_marshal
FROM Marshals M LEFT JOIN Persons_Marshals PM
ON M.id_marshal=PM.id_marshal AND id_person=:id_person
WHERE M.id_combat=:id_combat";
$data = [':id_person' => $id_person, ':id_combat' => $id_combat];
$sth = $cxn->prepare($query_marshal);
$sth->execute($data);

while ($row = $sth->fetch(PDO::FETCH_ASSOC)){
    extract($row);

    if (($row["name_marshal"] == "Rapier Marshal in Training") && ($row["id_person_marshal"] != NULL))
      {

          $warrants[] = "Rapier Marshal in Training";
      }
    if (($row["name_marshal"] == "Rapier Marshal") && ($row["id_person_marshal"] != NULL))
      {

          $warrants[] = "Rapier";
      }
    if (($row["name_marshal"] == "Group Rapier Marshal") && ($row["id_person_marshal"] != NULL))
      {

          $warrants[] = "Group Rapier";
      }
    if (($row["name_marshal"] == "Rapier Authorization Marshal") && ($row["id_person_marshal"] != NULL))
      {

          $warrants[] = "Rapier Auth";
      }
    if (($row["name_marshal"] == "Youth Rapier Marshal") && ($row["id_person_marshal"] != NULL))
      {

          $warrants[] = "Youth Rapier";
      }
    if (($row["name_marshal"] == "Cut and Thrust Marshal") && ($row["id_person_marshal"] != NULL))
      {

          $warrants[] = "Cut and Thrust";
      }
    if (($row["name_marshal"] == "Cut and Thrust Authorization Marshal") && ($row["id_person_marshal"] != NULL))
      {

          $warrants[] = "Cut and Thrust Auth";
      }
    if (($row["name_marshal"] == "Combat Archery Authorization Marshal") && ($row["id_person_marshal"] != NULL))
      {

          $warrants[] = "Combat Archery Auth";
      }
    if (($row["name_marshal"] == "Armored Combat Marshal") && ($row["id_person_marshal"] != NULL))
      {

          $warrants[] = "Armored Combat";
      }
    if (($row["name_marshal"] == "Fiberglass Spear Authorization Marshal") && ($row["id_person_marshal"] != NULL))
      {

          $warrants[] = "Fiberglass Spear Auth";
      }
    if (($row["name_marshal"] == "Siege Marshal") && ($row["id_person_marshal"] != NULL))
      {

          $warrants[] = "Siege";
      }
    if (($row["name_marshal"] == "Siege Authorization Marshal") && ($row["id_person_marshal"] != NULL))
      {

          $warrants[] = "Siege Auth";
      }

}

// get personal information

$query_fighter="SELECT name_person, name_mundane_person, membership_person, membership_expire_person, expire_authorize, expire_marshal
FROM Persons, Persons_CombatCards
WHERE Persons.id_person = Persons_CombatCards.id_person
AND id_combat=$id_combat
AND Persons.id_person=$id_person";
$data = [':id_combat' => $id_combat, ':id_person' => $id_person];
$sth = $cxn->prepare($query_fighter);
$sth->execute($data);

while ($row = $sth->fetch(PDO::FETCH_ASSOC)){
    extract($row);

    $sca_name = $row["name_person"];
    $mundane_name = $row["name_mundane_person"];
    $expire_marshal = $row["expire_marshal"];
    $expire_authorize = $row["expire_authorize"];
    $mem_number = $row["membership_person"];
    $mem_exp = $row["membership_expire_person"];
  }


// make the card
print "<section id=\"side1\">	<img src=\"ram.jpg\">"
    . "$itl The Society for Creative Anachronism <br/>"
    . "Combat Authorization Card <br />"
    . "Kingdom of Gleann Abhann $itlx <br /> <br />"
    . "<b>Sca Name: $sca_name </b><br />"
    . "Mundane Name: $mundane_name <br>"
    . "Auth Exp: $expire_authorize &nbsp; &nbsp; &nbsp; Marshal Exp: $expire_marshal<br>"
    . "<span id=\"spacing\">"
    . "Signature: ______________________________ </span><br />"
    . "<span class=\"small\"> This your authorization to participate on the field at SCA activities. It must be "
    . "presented to the List Officials at SCA events to register for participation and you may be "
    . "requested to show it to the marshals at any time. </span>"
    . "</sction>"
    . "<section id=\"side2\">"
    . "<div id=\"leftcol\">"
    . "<b>Authorized Styles:</b> <br />";
    if (!$authorizations)
    {
      echo "None.";
    }
    foreach ($authorizations as $auth)
    {
      if ($auth != NULL)
      {
        echo $auth . ', ';
      }
    }
  echo "</div><div id=\"rightcol\"><b>Warrants:</b> <br />";
  if (!$warrants)
  {
    echo "None.";
  }
    foreach ($warrants as $warrant)
      {
        if ($warrant != NULL)
        {echo $warrant . ', ';}
      }
  echo "</div></section>";

/* close the db connection 
 * normally footer.php will do this, however, we are not including the footer
 * in this document, so we have to close it here */
$sth = null;

?>
<a id="back" href="combat.php">Back to the Combat Page!</a>

</body>

</html>
