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
$query_comb = "SELECT id_combat, name_combat, cn, ea, ipcc, note, active "
        . "FROM Combat LEFT JOIN"
        . "(SELECT  id_person_combat_card as ipcc, card_marshal as cn, "
        . "expire_marshal as ea, id_combat as ic,"
        . "note_marshal as note, active_marshal as active "
        . "FROM  Persons_CombatCards "
        . "WHERE id_person=$id_person) AS PA "
        . "ON Combat.id_combat = PA.ic ORDER BY name_combat";
if (DEBUG) {
    echo "Per Category known facts:<br>$query_comb<p>";
}     
$combats = mysqli_query ($cxn, $query_comb) 
        or die ("Couldn't execute query to find known/current date/card numbers.");

echo form_title("Now updated Marshal's Warrants as follows.");
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
    $dynmidauth=NULL;
}

if (DEBUG) {
    if (isset($_POST['dynmact'])) { print_r($dynmact); echo " = dynmact<p>"; }
    print_r($dynmcombat); echo " = dynmcombat<p>";
    print_r($dynmdate); echo " = dynmdate <p>";
    print_r($dynmcard); echo " = dynmcard <p>";
    print_r($dynmnote); echo " = dynmnote <p>";
    print_r($dynmidauth); echo " = dynmidauth<p>";
}

// First we update expiry dates and card_numbers for each type of combat
// Note: card numbers are never deleted.
$i=0;
while ($row = mysqli_fetch_assoc($combats)){
    extract($row);
    // If the record exists in Person_Combatcards i.e. $ipcc != NULL
    //print_r($row);
    if (($dynmdate[$id_combat] != $ea) // change in expiry date
            || (isset($dynmact[$id_combat]) && ($dynmact[$id_combat] != $active)) // change in active status
            || ($dynmcard[$id_combat] != $cn) // change in card number
            || (strcmp($dynmnote[$id_combat],$note)!=0)){ // change in note
        if ($ipcc != NULL){// record exists; update if changed
             $update="UPDATE Persons_CombatCards "
                    . "SET expire_marshal='$dynmdate[$id_combat]'";
            if ($dynmcard[$id_combat]!= NULL) {
                $update=$update.", card_marshal=$dynmcard[$id_combat] ";
            }
            if ($dynmnote[$id_combat]!=$note){
                $update=$update.",note_marshal='".sanitize_mysql($dynmarshal[$id_combat])."'";
            }
            $update = $update.", active_marshal='$dynmact[$id_combat]' ";
            $update = $update. " WHERE id_person_combat_card=$ipcc;";
        } else { // record doesn't exist; insert if new data added
            $dynmact[$id_combat]='Yes';
            $update_head="INSERT INTO Persons_CombatCards "
                    . "(id_person, id_combat, active_marshal ";
            $update_tail="VALUES ($id_person, $id_combat,'Yes'";
            if ($dynmcard[$id_combat]!= NULL) {
                $update_head=$update_head.", card_marshal";
                $update_tail=$update_tail.", $dynmcard[$id_combat]";
            }
            if ($dynmdate[$id_combat]!= NULL) {
                $update_head=$update_head.", expire_marshal";
                $update_tail=$update_tail.", '$dynmdate[$id_combat]'";
            }
            if ($dynmnote[$id_combat]!= NULL) {
                $update_head=$update_head.", note_marshal";
                $update_tail=$update_tail.", '".sanitize_mysql($dynmnote[$id_combat])."'";
            }
            $update=$update_head.") ".$update_tail.")";
        }
        if (DEBUG) {
            echo "Update query for $name_combat is:$update<p>";
        }
        echo form_subtitle("Updated $name_combat warrant: "
                          . "expires on $dynmdate[$id_combat], card number $dynmcard[$id_combat],"
                . " currently active is $dynmact[$id_combat], and with note '"
                . sanitize_mysql($dynmnote[$id_combat])."'");
        $result=update_query($cxn, $update);
        if ($result !== 1) {echo "Error updating warrant date/card number: " . mysqli_error($cxn);}
    } // Else data wasn't changed so do nothing
    $i++;
}

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
        WHERE id_person=$id_person) AS PCC
   ON AC.id_combat=PCC.ic) AS ACPCC
LEFT JOIN
   ( SELECT id_marshal as ia, id_person as idp
     FROM Persons_Marshals
     WHERE id_person=$id_person) AS AU
ON ACPCC.ip=AU.idp AND ACPCC.id_marshal=AU.ia;";
if (DEBUG) {
    echo "Known Marshals Warrants:<br>$query_marshals<p>";
}     

$marshals = mysqli_query ($cxn, $query_marshals) 
        or die ("Couldn't execute query to find known/current warrants.");

while ($row = mysqli_fetch_assoc($marshals)) {
   // Each row represents one possible marshal's warrant identified by $id_marshal
   // If isset(dynmidauth[$id_marshal)) then the box for that marshal's warrant is ticked.
    extract($row);
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
       if ($ipcc != NULL) {
           echo " with card for $name_combat";
       }
       echo "<p>";
   }
   if ($ipcc == NULL){ // No combat card info means no warrants
       if (isset($dynmidauth[$id_marshal])) {
           echo form_subtitle("Cannot authorize $name_marshal "
                                . "until the card information for $name_combat combat "
                                . "is completed.<p>");
       }
   } else { // We have a combat card and can now start doing stuff.
       if (($ia != NULL) && !isset($dynmidauth[$id_marshal])) {
           // Authorization exists in database, but box is no longer ticked
           $update = "DELETE FROM Persons_Marshals "
                   . "WHERE id_person=$id_person "
                   . "AND id_marshal = $id_marshal";
           $result=update_query($cxn, $update);
           if (DEBUG) {
               echo "Deleting marshal's warrant: $update<p>";
           }
           if ($result !== 1) {
              echo "Error deleting $name_marshal marshal's warrants: ".mysqli_error($cxn);
           }
       }
       if (($ia == NULL) && isset($dynmidauth[$id_marshal])) {
           // Authorization needs to be added to the database
           $update="INSERT INTO Persons_Marshals "
                   . "(id_person_marshal, id_marshal, id_person) "
                   . "VALUES(NULL,$id_marshal,$id_person);";
           $result=update_query($cxn, $update);
           if (DEBUG) {
               echo "Adding marshal's warrant: $update<p>";
           }
           if ($result !== 1) {
              echo "Error adding $name_marshal authorizations: ".mysqli_error($cxn);
           }
       }
   }   
}
echo button_link("edit_person.php?id=$id_person", "Return to Edit Personal Page for $name_person");