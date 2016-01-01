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
$cxn = open_db_browse();
$query_comb = "SELECT id_combat, name_combat, cn, ea, ipcc, note FROM Combat LEFT JOIN"
        . "(SELECT  id_person_combat_card as ipcc, card_marshal as cn, "
        . "expire_marshal as ea, id_combat as ic,"
        . "note_marshal as note "
        . "FROM  Persons_CombatCards "
        . "WHERE id_person=$id_person) AS PA "
        . "ON Combat.id_combat = PA.ic ORDER BY name_combat";
$query_marshals = "SELECT * FROM "
        . "(SELECT id_marshal, name_marshal, Combat.id_combat, name_combat "
        . "FROM Marshals, Combat "
        . "WHERE Marshals.id_combat = Combat.id_combat "
        . "ORDER BY name_combat, name_marshal) AS AC "
        . "LEFT JOIN "
        . "(SELECT id_marshal as ia, id_person_marshal, expire_marshal, card_number "
        . "FROM Persons_Marshals where id_person=$id_person) AS PA "
        . "on AC.id_marshal = PA.ia";

if (DEBUG) {
    echo "Per Category known facts:<br>$query_comb<p>";
    echo "Known Marshals Warrants: <br>$query_marshals<p>";
}        
$marshals = mysqli_query ($cxn, $query_marshals) 
        or die ("Couldn't execute query to find known/current authorizations.");
$combats = mysqli_query ($cxn, $query_comb) 
        or die ("Couldn't execute query to find known/current date/card numbers.");

echo form_title("Now updated Marshal's Warrants as follows.");
$dynmcombat=$_POST['dynmcombat'];
$dynmdate=$_POST['dynmdate'];
$dynmcard=$_POST['dynmcard'];
$dynmnote=$_POST['dynmnote'];
if (isset($_POST['dynmidauth'])) { // Need to account for case where no checkmarks at all.
    $dynmidauth=$_POST['dynmidauth'];
} 

if (DEBUG) {
    print_r($dynmcombat); echo "<p>";
    print_r($dynmdate); echo "<p>";
    print_r($dynmcard); echo "<p>";
    print_r($dynmnote); echo "<p>";
}

// First we update expiry dates and card_numbers for each type of combat
// Note: card numbers are never deleted.
$i=0;
while ($row = mysqli_fetch_assoc($combats)){
    extract($row);
    //print_r($row);
    //echo "Note matches: ".  strcmp($dynmnote[$i], $note)."<p>";
    if (($dynmdate[$i] != $ea) || 
            ($dynmcard[$i] != $cn) || 
            (strcmp($dynmnote[$i],$note)!=0)){// if there are changes in the card number, date, or note
        if ($ipcc != NULL){// record exists; update if changed
            $update="UPDATE Persons_CombatCards "
                    . "SET expire_marshal='$dynmdate[$i]'";
            if ($dynmcard[$i]!= NULL) {
                $update=$update.", card_marshal=$dynmcard[$i] ";
            }
            if ($dynmnote[$i]!=$note){
                $update=$update.",note_marshal='".sanitize_mysql($dynmnote[$i])."'";
            }
            $update = $update. " WHERE id_person_combat_card=$ipcc;";
        } else { // record doesn't exist; insert if new data added
            $update_head="INSERT INTO Persons_CombatCards "
                    . "(id_person, id_combat ";
            $update_tail="VALUES ($id_person, $id_combat";
            if ($dynmcard[$i]!= NULL) {
                $update_head=$update_head.", card_marshal";
                $update_tail=$update_tail.", $dynmcard[$i]";
            }
            if ($dynmdate[$i]!= NULL) {
                $update_head=$update_head.", expire_marshal";
                $update_tail=$update_tail.", '$dynmdate[$i]'";
            }
            if ($dynmnote[$i]!= NULL) {
                $update_head=$update_head.", note_marshal";
                $update_tail=$update_tail.", '".sanitize_mysql($dynmnote[$i])."'";
            }
            $update=$update_head.") ".$update_tail.")";
        }
        if (DEBUG) {
            echo "Update query for $name_combat is:$update<p>";
        }
        echo form_subtitle("Updated $name_combat authorization: "
                          . "expires on $dynmdate[$i], card number $dynmcard[$i], with note '$dynmnote[$i]'");
        $result=update_query($cxn, $update);
        if ($result !== 1) {echo "Error updating authorization date/card number: " . mysqli_error($cxn);}
        
    } else {
        //$update="Data unchanged.<P>";
    }
    $i++;
}

// Now we update based on check marks.  Note that these entries *can* get deleted.
// if dynmidauth is not set, then no boxes were checked and all entries can be deleted in one mass update
if (!isset($dynmidauth)) {
    echo form_subtitle("No checkboxes ticked: deleting all authorizations");
    $update="DELETE FROM Persons_Marshals "
            . "WHERE id_person=$id_person";
    $result=update_query($cxn, $update);
    if ($result !== 1) {
        echo "Error deleting authorizations: ".mysqli_error($cxn);
    }
} else {
    $i=0;
    while ($row = mysqli_fetch_assoc($marshals)){
        extract($row);
        if (DEBUG) {echo "$dynmcombat[$i] == $id_combat:";}
        if ($id_combat != $dynmcombat[$i]) {
            $i++;
        }
        if (isset($dynmidauth[$id_marshal])) {
            //echo "$name_marshal is checked: ";
            if ($id_person_marshal != NULL){
                //echo "Need to check to see if record needs updating.<p>";
                //echo "$dynmdate[$i] == $expire_marshal, $dynmcard[$i]==$card_number<p>";
                if (($dynmdate[$i]!= $expire_marshal) || ($dynmcard[$i]!= $card_number)) {
                    $update = "UPDATE Persons_Marshals "
                            . "SET expire_marshal='$dynmdate[$i]'";
                    if ($dynmcard[$i] != NULL) {
                        $update = $update.", card_number=$dynmcard[$i] ";
                    }
                    $update = $update." WHERE id_person_marshal=$id_person_marshal";
                    //echo "Update query is: $update<p>";
                    $result=update_query($cxn, $update);
                    if ($result !== 1) {echo "Error updating record: " . mysqli_error($cxn);}
                    echo form_subtitle("Updated $name_marshal authorization "
                                . "to expire on $dynmdate[$i] with card number $dynmcard[$i]");
                } else {
                    // echo "Both expiration date and card number match<p>";
                }
            } else {
//                $update="INSERT INTO Persons_Marshals "
//                        . "(id_person, id_marshal, expire_marshal, card_number) "
//                        . "VALUES ($id_person, $id_marshal, '$dynmdate[$i]', $dynmcard[$i])";
//                
                $update_head="INSERT INTO Persons_Marshals (id_person, id_marshal";
                $update_tail="VALUES ($id_person, $id_marshal";
                if ($dynmcard[$i]!= NULL) {
                    $update_head=$update_head.", card_marshal";
                    $update_tail=$update_tail.", $dynmcard[$i]";
                }
                if ($dynmdate[$i]!= NULL) {
                    $update_head=$update_head.", expire_marshal";
                    $update_tail=$update_tail.", '$dynmdate[$i]'";
                }
                $update=$update_head.") ".$update_tail.")";
                $result=update_query($cxn, $update);
                if ($result !== 1) {
                    echo "Error adding authorizations: ".mysqli_error($cxn);
                    if (DEBUG){echo "<p>Query was: $update<p>";}
                }
                //echo "Need to create record: $update<p>";
                echo form_subtitle("Added a warrant for $name_marshal ($name_combat), expiring $dynmdate[$i]");
            }
        } else {
            //echo "$name_marshal is not checked: ";
            if ($ia != NULL){
                //echo "Need to delete record<p>";
                $update = "DELETE FROM Persons_Marshals "
                        . "WHERE id_person_marshal=$id_person_marshal";
                $result=update_query($cxn, $update);
                if ($result !== 1) {
                    echo "Error deleting authorizations: ".mysqli_error($cxn);
                }
                echo form_subtitle("Deleted authorization for $name_marshal ($name_combat).");
            } else {
                //echo "Need to do nothing<p>";
            }
        }
    }
}

echo button_link("edit_person.php?id=$id_person", "Return to Edit Personal Page for $name_person");