<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (permissions("Marshal")< 3) {
    //echo var_dump($_SESSION);
    bs_alert('<span class="error"> This page has been accessed in error.</span>', 'warning');
    exit_with_footer();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    bs_alert('<span class="error"> This page has been accessed in error.</span>', 'warning');
    exit_with_footer();
}

/* header has opened the db for us */

// Since we have the right permissions and arrived here via post,
// we will now update the database
$id_person = $_POST["id"];
$name_person = $_POST["name_person"];
$query_comb = "SELECT id_combat, name_combat, cn, ea, ipcc, note, active "
        . "FROM Combat LEFT JOIN "
        . "(SELECT  id_person_combat_card as ipcc, card_authorize as cn, "
        . "expire_authorize as ea, id_combat as ic, note_authorize as note, "
        . "active_authorize as active "
        . "FROM  Persons_CombatCards "
        . "WHERE id_person=:id_person) AS PA "
        . "ON Combat.id_combat = PA.ic "
        . "where id_combat in (select id_combat from Authorizations) "
        . "ORDER BY name_combat ";
$data_comb = [':id_person' => $id_person];
if (DEBUG) {
    echo "<p>Per Category known facts:<br/>$query_comb<br/>Vars:<br/>" . json_encode($data_comb) . "</p>";
}
try {
    $sth_combats = $cxn->prepare($query_comb);
    $sth_combats->execute($data_comb);
} catch (PDOException $e) {
    $error = "Couldn't execute query to find known/current date/card numbers.";
    if (DEBUG) {
        $message = $e->getMessage();
        $code = (int)$e->getCode();
        $error = "$error ($message / $code)";
    }
    bs_alert($error, 'warning');
}

echo "<h1>Card Information Updated as follows:</h1>";
if (isset($_POST['dynact'])) {
    $dynact=$_POST['dynact'];
}
$dyncombat=$_POST['dyncombat'];
$dyndate=$_POST['dyndate'];
$dyncard=$_POST['dyncard'];
$dynnote=$_POST['dynnote'];
if (isset($_POST['dynidauth'])) { // Need to account for case where no checkmarks at all.
    $dynidauth=$_POST['dynidauth'];
} else {
    $dynidauth=null;
}

if (DEBUG) {
    if (isset($_POST['dynact'])) {
        print_r($dynact);
        echo " = dynact<p>";
    }
    print_r($dyncombat);
    echo " = dyncombat<p>";
    print_r($dyndate);
    echo " = dyndate<p>";
    print_r($dyncard);
    echo " = dyncard<p>";
    print_r($dynnote);
    echo " = dynnote<p>";
    print_r($dynidauth);
    echo " = dynidauth<p>";
}

// First we update expiry dates and card_numbers for each type of combat
// Note: card numbers are never deleted.
$i=0;
echo "<ul>";
while ($row = $sth_combats->fetch()) {
    extract($row);
    // If the record exists in Person_Combatcards i.e. $ipcc != NULL
    // echo "Row being processed is ";print_r($row);
    $data = []; //init the data array
    if (($dyndate[$id_combat] != $ea) // change in expiry date
            || (isset($dynact[$id_combat]) && ($dynact[$id_combat] != $active)) // change in active status
            || ($dyncard[$id_combat] != $cn) // change in card number
            || (strcmp($dynnote[$id_combat], $note)!=0)) { // change in note
        if ($ipcc != null) {// record exists; update if changed
            $update="UPDATE Persons_CombatCards "
              . "SET expire_authorize=:dyndate";
            $data[':dyndate'] = $dyndate[$id_combat];
            if ($dyncard[$id_combat]!= null) {
                $update=$update.", card_authorize=:dyncard";
                $data[':dyncard'] = $dyncard[$id_combat];
            }
            if ($dynnote[$id_combat]!=$note) {
                $update=$update.", note_authorize=:dynnote";
                $data[':dynnote'] = $dynnote[$id_combat];
            }
            $update = $update.", active_authorize=:dynact";
            $data[':dynact'] = $dynact[$id_combat];
            $update = $update. " WHERE id_person_combat_card=:ipcc;";
            $data[':ipcc'] = $ipcc;
        } else {// record doesn't exist; insert if new data added
            $dynact[$id_combat]='Yes';
            $update_head="INSERT INTO Persons_CombatCards "
                    . "(id_person, id_combat, active_authorize ";
            $update_tail="VALUES (:id_person, :id_combat, 'Yes'";
            $data[':id_person'] = $id_person;
            $data[':id_combat'] = $id_combat;
            if ($dyncard[$id_combat]!= null) {
                $update_head=$update_head.", card_authorize";
                $update_tail=$update_tail.", :dyncard";
                $data[':dyncard'] = $dyncard[$id_combat];
            }
            if ($dyndate[$id_combat]!= null) {
                $update_head=$update_head.", expire_authorize";
                $update_tail=$update_tail.", :dyndate";
                $data[':dyndate'] = $dyndate[$id_combat];
            }
            if ($dynnote[$id_combat]!= null) {
                $update_head=$update_head.", note_authorize";
                $update_tail=$update_tail.", :dynnote";
                $data[':dynnote'] = $dynnote[$id_combat];
            }
            $update=$update_head.") ".$update_tail.")";
        }
        if (DEBUG) {
            echo "<p>Update query for $name_combat is:$update<br/>Vars are " . json_encode($data) . "</p>";
        }
        try {
            update_query($cxn, $update, $data);
            echo "<li class='bg-success'>Updated $name_combat authorization: <ul>"
             . "<li>expires on $dyndate[$id_combat]</li><li>card number $dyncard[$id_combat]</li>"
             . " <li>currently active is $dynact[$id_combat]</li><li> and with note '"
             . sanitize_mysql($dynnote[$id_combat])."'</li></ul></li>";
        } catch (PDOException $e) {
            $error = "Error updating card active status, expiration, card number, or note for $name_combat.";
            if (DEBUG) {
                $message = $e->getMessage();
                $code = (int)$e->getCode();
                $error = $error . " ($message / $code) ";
            }
            echo "<li class='bg-danger'>$error</li>";
        }
    } // Else data wasn't changed so do nothing
    $i++;
}
echo "</ul>";
echo "<h2>Updated Authorizations as Follows:</h2>";
echo "<ul>";
// Now we update based on check marks.  Note that these entries *can* get deleted.
// if dynidauth is not set, then no boxes were checked and all entries can be deleted
// in one mass update
// NEED TO ADD CHECKING SO THAT Persons_CombatCard has to have entry before we update
// NOTE: We delay query to here, so Persons_CombatCards table is already update
$query_auths = "SELECT * FROM
   (SELECT * FROM 
       (SELECT id_auth, name_auth, Combat.id_combat, name_combat 
        FROM Authorizations, Combat 
        WHERE Authorizations.id_combat = Combat.id_combat 
        ORDER BY name_combat, name_auth) AS AC
   LEFT JOIN 
        (SELECT id_person_combat_card as ipcc, id_person as ip, 
           expire_authorize as p_ea, card_authorize as p_cn, id_combat as ic
        FROM Persons_CombatCards
        WHERE id_person=:id_person) AS PCC
   ON AC.id_combat=PCC.ic) AS ACPCC
LEFT JOIN
   ( SELECT id_auth as ia, id_person as idp
     FROM Persons_Authorizations
     WHERE id_person=:id_pers) AS AU
ON ACPCC.ip=AU.idp AND ACPCC.id_auth=AU.ia;";
/* note: the same value is inserted in two locations in the query, but prepared
 * statements require that all named tokens used in a query be unique, so the
 * second instance of passing in the value of $id_person is renamed to `id_pers`
 * in the query and data array to satisfy the token uniqueness requirement */
$data_auths = [':id_person' => $id_person, ':id_pers' => $id_person];
if (DEBUG) {
    echo "Known authorizations: <br>$query_auths<p>";
}
try {
    $sth_auths = $cxn->prepare($query_auths);
    $sth_auths->execute($data_auths);
} catch (PDOException $e) {
    $error = "Couldn't execute query to find known/current authorizations.";
    if (DEBUG) {
        $message = $e->getMessage();
        $code = $e->getCode();
        $error = $error . " ($message / $code) ";
    }
    bs_alert($error, 'warning');
    exit_with_footer();
}
$comb_name = null;
while ($row = $sth_auths->fetch()) {
    // Each row represents one possible authorization identified by $id_auth
    // If isset(dynidauth[$id_auth)) then the box for that authorization is ticked.
    extract($row);
    // smartly handle combat names
    if (isset($comb_name)) {
        if ($comb_name != $name_combat) {
            // combat name changed, let's start a new sublist
            $comb_name = $name_combat;
            echo "</ul></li><li>$name_combat<ul>";
        }
    } else {
        //this is our first trip through, open the first sublist
        $comb_name = $name_combat;
        echo "<li>$name_combat<ul>";
    }

    // If there is no entry in Persons_CombatCards for that authorization and the box is ticked,
    //   then we throw up an error message.
    // If there is no entry and the box is not ticked, no worries.
    if (DEBUG) {
        echo "Now testing $name_auth: dynidauth[$id_auth]=";
        if (isset($dynidauth[$id_auth])) {
            echo $dynidauth[$id_auth];
        } else {
            echo "unset";
        }
        if ($ipcc != null) {
            echo " with card for $name_combat";
        }
        echo "<p>";
    }
    if ($ipcc == null) {// No combat card info means no authorizations
        if (isset($dynidauth[$id_auth])) {
            echo "<li class='bg-warning'>Cannot authorize $name_auth "
              . "until the card information for $name_combat combat "
              . "is completed.</li>";
        }
    } else { // We have a combat card and can now start doing stuff.
        if (($ia != null) && !isset($dynidauth[$id_auth])) {
            // Authorization exists in database, but box is no longer ticked
            $update = "DELETE FROM Persons_Authorizations "
                . "WHERE id_person=:id_person "
                . "AND id_auth = :id_auth";
            $data = [':id_person' => $id_person, ':id_auth' => $id_auth];
            try {
                if (DEBUG) {
                    echo "Attempting to delete authorization: $update<p>";
                }

                update_query($cxn, $update, $data);
                echo "<li class='bg-success'>Removed $name_auth</li>";
            } catch (PDOException $e) {
                $error = "Error deleting $name_auth authorizations. ";
                if (DEBUG) {
                    $message = $e->getMessage();
                    $code = (int)$e->getCode();
                    $error = $error . " ($message / $code) ";
                }
                echo "<li class='bg-danger'>$error</li>";
            }
        }
        if (($ia == null) && isset($dynidauth[$id_auth])) {
            // Authorization needs to be added to the database
            $update="INSERT INTO Persons_Authorizations "
                . "(id_person_auth, id_auth, id_person) "
                . "VALUES(NULL,:id_auth,:id_person);";
            $data = [':id_auth' => $id_auth, ':id_person' => $id_person];
            try {
                if (DEBUG) {
                    echo "Attempting to add authorization: $update<p>";
                }

                update_query($cxn, $update, $data);
                echo "<li class='bg-success'>Added $name_auth</li>";
            } catch (PDOException $e) {
                $error = "Error adding $name_auth authorizations.";
                if (DEBUG) {
                    $message = $e->getMessage();
                    $code = (int)$e->getCode();
                    $error = $error . "($message / $code)";
                }
                echo "<li class='danger'>$error</li>";
            }
        }
    }
}
echo "</ul></li>"; //close combat-type list

echo "</ul>";


echo button_link("edit_person.php?id=$id_person", "Return to Edit Personal Page for $name_person");
