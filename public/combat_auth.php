<?php

require("../includes/config.php");
echo "
<!doctype html>
<META HTTP-EQUIV=\"CACHE-CONTROL\" CONTENT=\"NO-CACHE\">
<meta http-equiv=\"expires\" content=\"0\" />
<html lang=\"en\">
<head>
  <title>  </title>
  <link rel=\"stylesheet\" href=\"/css/auth.css\">
</head>
<body>";

// NOTE: DO NOT AT THIS POINT USE ANY CLASS REFERENCES
// configuration
$cxn = open_db_browse();

echo form_title("Fighter Authorization Card");
echo "<div><p><a href=\"combat.php\">Return to the Combat Page</a></p></div>";

// First: confirm that we reached this page through a POST submission

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST["mem_num"]) && is_numeric($_POST["mem_num"])) {
         $mem_num = $_POST["mem_num"];
    } else {
    echo form_subtitle('Must include fighter membership number to print fighter authorization card');
    exit_with_footer();
    }
    if (isset($_POST["id_combat"])  && is_numeric($_POST["id_combat"])) {
        $id_combat=$_POST["id_combat"];
    }
} else {
    echo form_subtitle("Page accessed in error.");
    exit_with_footer();
}

// Confirm that there is a fighter authorization card on file for this person and combat type
$query = "SELECT Persons.id_person, id_person_combat_card, Combat.id_combat, name_combat "
        . "FROM Persons, Persons_CombatCards, Combat "
        . "WHERE Persons.id_person=Persons_CombatCards.id_person "
        . "AND membership_person=:mem_num "
        . "AND Combat.id_combat = Persons_CombatCards.id_combat "
        . "AND Combat.id_combat=:id_combat;";
$data = [':mem_num' => $mem_num, ':id_combat' => $id_combat];
$sth = $cxn->prepare($query);
$sth->execute($data);

// Now check: if we returned a result we have id_person.  If not, exit out with card not found.
// Note: we will return either 1 row, or 0.
$num_rows= $sth->rowCount();
if ($num_rows < 1){
    echo "Couldn't find authorization card given Combat type and Membership Number.<p>";
    echo '<a id="back" href="combat.php">Back to the Combat Page!</a>';
    exit_with_footer();
}
$row = $sth->fetch(PDO::FETCH_ASSOC);
$id_person=$row["id_person"];
$name_combat=$row["name_combat"];
//$id_person=764;

$query_fighter="SELECT name_person, name_mundane_person, membership_person,
    membership_expire_person,
    if(expire_authorize>=NOW(), expire_authorize,'NONE') as expire_authorize,
    if(expire_marshal>=NOW(), expire_marshal, 'NONE') as expire_marshal
    FROM Persons, Persons_CombatCards
    WHERE Persons.id_person = Persons_CombatCards.id_person
    AND id_combat=:id_combat
    AND Persons.id_person=:id_person";
$data = [':id_combat' => $id_combat, ':id_person' => $id_person];
$sth = $cxn->prepare($query_fighter);
$sth->execute($data);
$rowf = $sth->fetch(PDO::FETCH_ASSOC);
$SCA_name=$rowf["name_person"];
$mundane_name=$rowf["name_mundane_person"];
$expire_authorize=$rowf["expire_authorize"];
$expire_marshal=$rowf["expire_marshal"];

$query_auth="SELECT A.id_auth, A.name_auth, A.id_combat, PA.id_person_auth
    FROM Authorizations A LEFT JOIN Persons_Authorizations PA
    ON A.id_auth=PA.id_auth AND id_person=:id_person
    WHERE A.id_combat=:id_combat";
$data = [':id_person' => $id_person, ':id_combat' => $id_combat];
$sth_auth = $cxn->prepare($query_auth);
$sth_auth->execute($data);

$query_marshal="SELECT M.id_marshal, M.name_marshal, M.id_combat, PM.id_person_marshal
    FROM Marshals M LEFT JOIN Persons_Marshals PM
    ON M.id_marshal=PM.id_marshal AND id_person=:id_person
    WHERE M.id_combat=:id_combat";
$data = [':id_person' => $id_person, ':id_combat' => $id_combat];
$sth_marshal = $cxn->prepare($query_marshal);
$sth_marshal->execute($data);

        echo "<section id=\"side1\">";

        echo "<p style=\"text-align: center\"><b>The Society for Creative Anachronism</b><br/>
            <i>$name_combat Authorization Card <br />
            Kingdom of Gleann Abhann</i><br /></p>";
        echo "<b>SCA Name: $SCA_name </b><br />"
                . "Mundane Name: $mundane_name <br />"
                . "Auth Exp: $expire_authorize &nbsp; &nbsp; Marshal Exp: $expire_marshal"
                . "<p class=\"spacing\">Signature: ______________________________ </p>";
        echo "<span class=\"small\"> This is your authorization to participate on the field at SCA activities. It must be
        presented to the List Officials at SCA events to register for participation and you may be
        requested to show it to the marshals at any time. </span>";
        echo "</section><section id=\"side2\">
          <div id=\"leftcol\"><b>Authorizations:</b><br/>"; // Authorizations
                while ($row = $sth_auth->fetch(PDO::FETCH_ASSOC)){
                    echo "* ".$row["name_auth"].": ";
                    if ($row["id_person_auth"] == NULL) {
                        echo "NO<br/>";
                    } else {
                        echo "YES<br/>";
                    }
                }
                echo "</div><div id=\"rightcol\">";
                echo "<b>Warrants:</b> <br />"; // Marshals' Warrants
                while ($row = $sth_marshal->fetch(PDO::FETCH_ASSOC)){
                    echo "* ".$row["name_marshal"].": ";
                    if ($row["id_person_marshal"] == NULL) {
                        echo "NO<br/>";
                    } else {
                        echo "YES<br/>";
                    }
                }

        echo "</div></section>";
?>
