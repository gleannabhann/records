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
$query_comb = "SELECT id_combat, name_combat, cn, ea, ipcc, note "
        . "FROM Combat LEFT JOIN "
        . "(SELECT  id_person_combat_card as ipcc, card_authorize as cn, "
        . "expire_authorize as ea, id_combat as ic, note_authorize as note "
        . "FROM  Persons_CombatCards "
        . "WHERE id_person=$id_person) AS PA "
        . "ON Combat.id_combat = PA.ic ORDER BY name_combat ";
$query_auths = "SELECT * FROM "
        . "(SELECT id_auth, name_auth, Combat.id_combat, name_combat "
        . "FROM Authorizations, Combat "
        . "WHERE Authorizations.id_combat = Combat.id_combat "
        . "ORDER BY name_combat, name_auth) AS AC "
        . "LEFT JOIN "
        . "(SELECT id_auth as ia, id_person_auth, expire_auth, card_number "
        . "FROM Persons_Authorizations where id_person=$id_person) AS PA "
        . "on AC.id_auth = PA.ia";
if (DEBUG) {
    echo "Per Category known facts:<br>$query_comb<p>";
    echo "Known authorizations: <br>$query_auths<p>";
}        
$auths = mysqli_query ($cxn, $query_auths) 
        or die ("Couldn't execute query to find known/current authorizations.");
$combats = mysqli_query ($cxn, $query_comb) 
        or die ("Couldn't execute query to find known/current date/card numbers.");

echo form_title("Now updated Authorizations as follows.");
$dyncombat=$_POST['dyncombat'];
$dyndate=$_POST['dyndate'];
$dyncard=$_POST['dyncard'];
$dynnote=$_POST['dynnote'];
if (isset($_POST['dynidauth'])) { // Need to account for case where no checkmarks at all.
    $dynidauth=$_POST['dynidauth'];
} 

if (DEBUG) {
    print_r($dyncombat); echo "<p>";
    print_r($dyndate); echo "<p>";
    print_r($dyncard); echo "<p>";
    print_r($dynnote); echo "<p>";
}

// First we update expiry dates and card_numbers for each type of combat
// Note: card numbers are never deleted.
$i=0;
while ($row = mysqli_fetch_assoc($combats)){
    extract($row);
    //print_r($row);
    if (($dyndate[$i] != $ea) // change in expiry date
            || ($dyncard[$i] != $cn) // change in card number
            || (strcmp($dynnote[$i],$note)!=0)){ // change in note
        if ($ipcc != NULL){// record exists; update if changed
            $update="UPDATE Persons_CombatCards "
                    . "SET expire_authorize='$dyndate[$i]'";
            if ($dyncard[$i]!= NULL) {
                $update=$update.", card_authorize=$dyncard[$i] ";
            }
            if ($dynnote[$i]!=$note){
                $update=$update.",note_authorize='".sanitize_mysql($dynnote[$i])."'";
            }
            $update = $update. " WHERE id_person_combat_card=$ipcc;";
        } else { // record doesn't exist; insert if new data added
//            $update="INSERT INTO Persons_CombatCards "
//                    . "(id_person, id_combat, card_authorize,expire_authorize,note_authorize) "
//                    . "VALUES ($id_person, $id_combat,$dyncard[$i] , '$dyndate[$i]','"
//                    . sanitize_mysql($dynnote[$i])."')";
            $update_head="INSERT INTO Persons_CombatCards "
                    . "(id_person, id_combat ";
            $update_tail="VALUES ($id_person, $id_combat";
            if ($dyncard[$i]!= NULL) {
                $update_head=$update_head.", card_authorize";
                $update_tail=$update_tail.", $dyncard[$i]";
            }
            if ($dyndate[$i]!= NULL) {
                $update_head=$update_head.", expire_authorize";
                $update_tail=$update_tail.", '$dyndate[$i]'";
            }
            if ($dynnote[$i]!= NULL) {
                $update_head=$update_head.", note_authorize";
                $update_tail=$update_tail.", '".sanitize_mysql($dynnote[$i])."'";
            }
            $update=$update_head.") ".$update_tail.")";
            
        }
        if (DEBUG) {
            echo "Update query for $name_combat is:$update<p>";
        }
        echo form_subtitle("Updated $name_combat authorization: "
                          . "expires on $dyndate[$i], card number $dyncard[$i], with note '"
                . sanitize_mysql($dynnote[$i])."'");
        $result=update_query($cxn, $update);
        if ($result !== 1) {echo "Error updating authorization date/card number: " . mysqli_error($cxn);}
        
    } else {
        //$update="Data unchanged.<P>";
    }
    $i++;
}

// Now we update based on check marks.  Note that these entries *can* get deleted.
// if dynidauth is not set, then no boxes were checked and all entries can be deleted in one mass update
if (!isset($dynidauth)) {
    echo form_subtitle("No checkboxes ticked: deleting all authorizations");
    $update="DELETE FROM Persons_Authorizations "
            . "WHERE id_person=$id_person";
    $result=update_query($cxn, $update);
    if ($result !== 1) {
        echo "Error deleting authorizations: ".mysqli_error($cxn);
    }
} else {
    $i=0;
    while ($row = mysqli_fetch_assoc($auths)){
        extract($row);
        if (DEBUG) {echo "$dyncombat[$i] == $id_combat:";}
        if ($id_combat != $dyncombat[$i]) {
            $i++;
        }
        if (isset($dynidauth[$id_auth])) {
            //echo "$name_auth is checked: ";
            if ($id_person_auth != NULL){
                //echo "Need to check to see if record needs updating.<p>";
                //echo "$dyndate[$i] == $expire_auth, $dyncard[$i]==$card_number<p>";
                if (($dyndate[$i]!= $expire_auth) || ($dyncard[$i]!= $card_number)) {
                    $update = "UPDATE Persons_Authorizations "
                            . "SET expire_auth='$dyndate[$i]'";
                    if ($dyncard[$i] != NULL) {
                        $update = $update.", card_number=$dyncard[$i] ";
                    }
                    $update = $update." WHERE id_person_auth=$id_person_auth";
                    //echo "Update query is: $update<p>";
                    $result=update_query($cxn, $update);
                    if ($result !== 1) {echo "Error updating record: " . mysqli_error($cxn);}
                    echo form_subtitle("Updated $name_auth authorization "
                                . "to expire on $dyndate[$i] with card number $dyncard[$i]");
                } else {
                    // echo "Both expiration date and card number match<p>";
                }
            } else {
//                $update="INSERT INTO Persons_Authorizations "
//                        . "(id_person, id_auth, expire_auth, card_number) "
//                        . "VALUES ($id_person, $id_auth, '$dyndate[$i]', $dyncard[$i])";
                $update_head="INSERT INTO Persons_CombatCards "
                        . "(id_person, id_combat ";
                $update_tail="VALUES ($id_person, $id_combat";
                if ($dyncard[$i]!= NULL) {
                    $update_head=$update_head.", card_authorize";
                    $update_tail=$update_tail.", $dyncard[$i]";
                }
                if ($dyndate[$i]!= NULL) {
                    $update_head=$update_head.", expire_authorize";
                    $update_tail=$update_tail.", '$dyndate[$i]'";
                }
                $update=$update_head.") ".$update_tail.")";
                
                $result=update_query($cxn, $update);
                if ($result !== 1) {
                    echo "Error adding authorizations: ".mysqli_error($cxn);
                }
                //echo "Need to create record: $update<p>";
                echo form_subtitle("Added an authorization for $name_auth ($name_combat), expiring $dyndate[$i]");
            }
        } else {
            //echo "$name_auth is not checked: ";
            if ($ia != NULL){
                //echo "Need to delete record<p>";
                $update = "DELETE FROM Persons_Authorizations "
                        . "WHERE id_person_auth=$id_person_auth";
                $result=update_query($cxn, $update);
                if ($result !== 1) {
                    echo "Error deleting authorizations: ".mysqli_error($cxn);
                }
                echo form_subtitle("Deleted authorization for $name_auth ($name_combat).");
            } else {
                //echo "Need to do nothing<p>";
            }
        }
    }
}

echo button_link("edit_person.php?id=$id_person", "Return to Edit Personal Page for $name_person");