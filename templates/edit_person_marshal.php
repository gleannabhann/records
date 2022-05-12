<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (permissions("Marshal")< 3) {
    //echo var_dump($_SESSION);
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}

// Since we have the right permissions and arrived here via post,
// we will now update the database
$id_person = $_POST["id"];
$name_person = $_POST["name_person"];
/* header.php and header_main.php connect us to the browser */
$query_comb = "SELECT id_combat, name_combat, cn, ea, ipcc, note, active "
        . "FROM Combat LEFT JOIN"
        . "(SELECT  id_person_combat_card as ipcc, card_marshal as cn, "
        . "expire_marshal as ea, id_combat as ic,"
        . "note_marshal as note, active_marshal as active "
        . "FROM  Persons_CombatCards "
        . "WHERE id_person=:id_person) AS PA "
        . "ON Combat.id_combat = PA.ic ORDER BY name_combat";
$data_comb = [':id_person' => $id_person];
if (DEBUG) {
    echo "<p>Per Category known facts:<br/>$query_comb<br/>Vars:<br/>" . json_encode($data_comb) . "</p>";
}

try {
    $sth_combats = $cxn->prepare($query_comb);
    $sth_combats->execute($data_comb);
} catch (PDOException $e) {
    $error = "Couldn't fetch known/current date/card numbers. ";
    if (DEBUG) {
        $error = add_pdo_exception($error, $e);
    }
    bs_alert($error, 'warning');
    exit_with_footer();
}


echo "<h1>Updated Marshal's Warrants as Follows</h1>";
if (isset($_POST['dynmact'])) {
    $dynmact=$_POST['dynmact'];
}
$dynmcombat=$_POST['dynmcombat'];
$dynmdate=$_POST['dynmdate'];
$dynmcard=$_POST['dynmcard'];
$dynmnote=$_POST['dynmnote'];
if (isset($_POST['dynmidauth'])) { // Need to account for case where no checkmarks at all.
    $dynmidauth=$_POST['dynmidauth'];
} else {
    $dynmidauth=null;
}

if (DEBUG) {
    if (isset($_POST['dynmact'])) {
        print_r($dynmact);
        echo " = dynmact<p>";
    }
    print_r($dynmcombat);
    echo " = dynmcombat<p>";
    print_r($dynmdate);
    echo " = dynmdate <p>";
    print_r($dynmcard);
    echo " = dynmcard <p>";
    print_r($dynmnote);
    echo " = dynmnote <p>";
    print_r($dynmidauth);
    echo " = dynmidauth<p>";
}

// First we update expiry dates and card_numbers for each type of combat
// Note: card numbers are never deleted.
$i=0;
echo "<ul>";
while ($row = $sth_combats->fetch()) {
    extract($row);
    // If the record exists in Person_Combatcards i.e. $ipcc != NULL
    //print_r($row);
    $data = [];
    if (($dynmdate[$id_combat] != $ea) // change in expiry date
            || (isset($dynmact[$id_combat]) && ($dynmact[$id_combat] != $active)) // change in active status
            || ($dynmcard[$id_combat] != $cn) // change in card number
            || (strcmp($dynmnote[$id_combat], $note)!=0)) { // change in note
        if ($ipcc != null) {// record exists; update if changed
             $update="UPDATE Persons_CombatCards "
               . "SET expire_marshal=:dynmdate";
            $data[':dynmdate'] = $dynmdate[$id_combat];
            if ($dynmcard[$id_combat]!= null) {
                $update=$update.", card_marshal=:dynmcard ";
                $data[':dynmcard'] = $dynmcard[$id_combat];
            }
            if ($dynmnote[$id_combat]!=$note) {
                $update=$update.",note_marshal=:dynmnote";
                $data[':dynmnote'] = $dynmnote[$id_combat];
            }
            $update = $update.", active_marshal=:dynmact ";
            $data[':dynmact'] = $dynmact[$id_combat];
            $update = $update. " WHERE id_person_combat_card=:ipcc;";
            $data[':ipcc'] = $ipcc;
        } else { // record doesn't exist; insert if new data added
            $dynmact[$id_combat]='Yes';
            $update_head="INSERT INTO Persons_CombatCards "
                    . "(id_person, id_combat, active_marshal ";
            $update_tail="VALUES (:id_person, :id_combat,'Yes'";
            $data[':id_person'] = $id_person;
            $data[':id_combat'] = $id_combat;
            if ($dynmcard[$id_combat]!= null) {
                $update_head=$update_head.", card_marshal";
                $update_tail=$update_tail.", :dynmcard";
                $data[':dynmcard'] = $dynmcard[$id_combat];
            }
            if ($dynmdate[$id_combat]!= null) {
                $update_head=$update_head.", expire_marshal";
                $update_tail=$update_tail.", :dynmdate";
                $data['dynmdate'] = $dynmdate[$id_combat];
            }
            if ($dynmnote[$id_combat]!= null) {
                $update_head=$update_head.", note_marshal";
                $update_tail=$update_tail.", :dynmnote";
                $data['dynmnote'] = $dynmnote[$id_combat];
            }
            $update=$update_head.") ".$update_tail.")";
        }
        if (DEBUG) {
            echo "<p>Update query for $name_combat is:<br/>$update<br/>Vars:<br/>" . json_encode($data) . "</p>";
        }

        try {
            update_query($cxn, $update, $data);
            echo "<li class='bg-success'>Updated $name_combat card: <ul>"
            . "<li>expires on $dynmdate[$id_combat]</li><li>card number $dynmcard[$id_combat]</li>"
             . " <li>currently active is $dynmact[$id_combat]</li><li> and with note '"
              . sanitize_mysql($dynmnote[$id_combat])."'</li></ul></li>";
        } catch (PDOException $e) {
            $error = "Error updating warrant date/card number for $name_combat. " ;
            if (DEBUG) {
                $error = add_pdo_exception($error, $e);
            }
            echo "<li class='bg-danger'>$error</li>";
        } // Else data wasn't changed so do nothing
    }
    $i++;
}
echo "</ul>";
echo "<h2>Updated Combat Sub-types as Follows:</h2>";
echo "<ul>";
// Now we update based on check marks.  Note that these entries *can* get deleted.
// if dynmidauth is not set, then no boxes were checked and all entries can be
// deleted in one mass update
// NEED TO ADD CHECKING SO THAT Persons_CombatCard has to have entry before we update
// NOTE: We delay query to here, so Persons_CombatCards table is already update
$query_marshals = "SELECT * FROM
   (SELECT * FROM 
       (SELECT id_marshal, name_marshal, Combat.id_combat, name_combat 
        FROM Marshals, Combat 
        WHERE Marshals.id_combat = Combat.id_combat 
        ORDER BY name_combat, name_marshal) AS AC
   LEFT JOIN 
        (SELECT id_person_combat_card as ipcc, id_person as ip, 
           expire_marshal as p_ea, card_marshal as p_cn, id_combat as ic
        FROM Persons_CombatCards
        WHERE id_person=:id_person) AS PCC
   ON AC.id_combat=PCC.ic) AS ACPCC
LEFT JOIN
   ( SELECT id_marshal as ia, id_person as idp
     FROM Persons_Marshals
     WHERE id_person=:id_pers) AS AU
ON ACPCC.ip=AU.idp AND ACPCC.id_marshal=AU.ia;";
// note that we need to pass the value of $id_person to the query twice, but
// named placeholders must be unique and the number of tokens in the vars array
// must match the number of placeholders. So, we will name one placeholder
// :id_person and the other :id_pers and we will assign the value of $id_person
// to both keys in the vars array
$data = [':id_person' => $id_person, ':id_pers' => $id_person];
if (DEBUG) {
    echo "<p>Known Marshals Warrants:<br>$query_marshals<br/>Vars:" . json_encode($data) . "</p>";
}

try {
    $sth = $cxn->prepare($query_marshals);
    $sth->execute($data);
} catch (PDOException $e) {
    $error = "Couldn't execute query to find known/current warrants. ";
    if (DEBUG) {
        $error = add_pdo_exception($error, $e);
    }
    echo "<li class='bg-warning'>$error</li>";
}
$comb_name = null;
while ($row = $sth->fetch()) {
    // Each row represents one possible marshal's warrant identified by $id_marshal
    // If isset(dynmidauth[$id_marshal)) then the box for that marshal's warrant is ticked.
    extract($row);
    // smartly handle combat names
    if (isset($comb_name)) {
        if ($comb_name != $name_combat) {
            //combat name changed, let's start a new sublist
            $comb_name = $name_combat;
            echo "</ul></li><li>$name_combat<ul>";
        }
    } else {
        // this is our first trip through, open the first sublist
        $comb_name = $name_combat;
        echo "<li>$name_combat<ul>";
    }

    // If there is no entry in Persons_CombatCards for that marshal's warrant and the box is ticked,
    //   then we throw up an error message.
    // If there is no entry and the box is not ticked, no worries.
    if (DEBUG) {
        echo "Now testing $name_marshal: dynmidauth[$id_marshal]=";
        if (isset($dynmidauth[$id_marshal])) {
            echo $dynmidauth[$id_marshal];
        } else {
            echo "unset";
        }
        if ($ipcc != null) {
            echo " with card for $name_combat";
        }
        echo "<p>";
    }
    if (isset($dynmdate[$id_marshal])) {
      $expires = new DateTime($dynmdate[$id_marshal]);
      $now = new DateTime();
      $days = intval($now->diff($expires)->format("%r%a"));
      echo "Days to expiration: $days<br/>";
    } else {
      // no expiration date set. We'll set the days to expiration to 0
      $days = 0;
    }
    if ($ipcc == null) { // No combat card info means no warrants
        if (isset($dynmidauth[$id_marshal]) || $days < 0) {
            echo "<li class='bg-warning'>Cannot authorize $name_marshal "
                                . "until the card information for $name_combat combat "
                                . "is completed.</li>";
        }
    } else { // We have a combat card and can now start doing stuff.
        if (($ia != null) && !isset($dynmidauth[$id_marshal])) {
            // Authorization exists in database, but box is no longer ticked
            $update = "DELETE FROM Persons_Marshals "
                . "WHERE id_person=:id_person "
                . "AND id_marshal = :id_marshal";
            $data = [':id_person' => $id_person, ':id_marshal' => $id_marshal];
            try {
                if (DEBUG) {
                    echo "Attempting to delete marshal's warrant: $update<p>";
                }
                update_query($cxn, $update, $data);
                echo "<li class='bg-success'>Removed $name_marshal</li>";
            } catch (PDOException $e) {
                $error = "Error deleting $name_marshal marshal's warrants. ";
                if (DEBUG) {
                    $error = add_pdo_exception($error, $e);
                }
                echo "li class='bg-danger'>$error</li>";
            }
        }
        if (($ia == null) && isset($dynmidauth[$id_marshal])) {
            // Authorization needs to be added to the database
            $update="INSERT INTO Persons_Marshals "
              . "(id_person_marshal, id_marshal, id_person) "
              . "VALUES(NULL, :id_marshal, :id_person);";
            $data = [':id_marshal' => $id_marshal, ':id_person' => $id_person];
            try {
                if (DEBUG) {
                    echo "<p>Attempting to add marshal's warrant:<br/>$update<br/>Using Vars:<br/>" . json_encode($data) . "</p>";
                }
                update_query($cxn, $update, $data);
                echo "<li class='bg-success'>Added $name_marshal</li>";
            } catch (PDOException $e) {
                $error = "Error adding $name_marshal authorizations. ";
                if (DEBUG) {
                    $error = add_pdo_exception($error, $e);
                }
                echo "<li class='bg-danger'>$error</li>";
            }
        }
    }
}
  echo "</ul></li>"; //close combat-type sublist
  echo "</ul>"; //close the outer list
echo button_link("edit_person.php?id=$id_person", "Return to Edit Personal Page for $name_person");
