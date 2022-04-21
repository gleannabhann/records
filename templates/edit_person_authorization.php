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
        . "FROM Combat LEFT JOIN "
        . "(SELECT  id_person_combat_card as ipcc, card_authorize as cn, "
        . "expire_authorize as ea, id_combat as ic, note_authorize as note, "
        . "active_authorize as active "
        . "FROM  Persons_CombatCards "
        . "WHERE id_person=$id_person) AS PA "
        . "ON Combat.id_combat = PA.ic "
        . "where id_combat in (select id_combat from Authorizations) "
        . "ORDER BY name_combat ";
if (DEBUG) {
    echo "Per Category known facts:<br>$query_comb<p>";
}        
$combats = mysqli_query ($cxn, $query_comb) 
        or die ("Couldn't execute query to find known/current date/card numbers.");

echo form_title("Now updated Authorizations as follows:");
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
    $dynidauth=NULL;
} 

if (DEBUG) {
    if (isset($_POST['dynact'])) { print_r($dynact); echo " = dynact<p>"; }
    print_r($dyncombat); echo " = dyncombat<p>";
    print_r($dyndate); echo " = dyndate<p>";
    print_r($dyncard); echo " = dyncard<p>";
    print_r($dynnote); echo " = dynnote<p>";
    print_r($dynidauth); echo " = dynidauth<p>";
}

// First we update expiry dates and card_numbers for each type of combat
// Note: card numbers are never deleted.
$i=0;
while ($row = mysqli_fetch_assoc($combats)){
    extract($row);
    // If the record exists in Person_Combatcards i.e. $ipcc != NULL
    // echo "Row being processed is ";print_r($row);
    if (($dyndate[$id_combat] != $ea) // change in expiry date
            || (isset($dynact[$id_combat]) && ($dynact[$id_combat] != $active)) // change in active status
            || ($dyncard[$id_combat] != $cn) // change in card number
            || (strcmp($dynnote[$id_combat],$note)!=0)){ // change in note
        if ($ipcc != NULL){// record exists; update if changed
            $update="UPDATE Persons_CombatCards "
                    . "SET expire_authorize='$dyndate[$id_combat]'";
            if ($dyncard[$id_combat]!= NULL) {
                $update=$update.", card_authorize=$dyncard[$id_combat] ";
            }
            if ($dynnote[$id_combat]!=$note){
                $update=$update.",note_authorize='".sanitize_mysql($dynnote[$id_combat])."'";
            }
            $update = $update.", active_authorize='$dynact[$id_combat]' ";
            $update = $update. " WHERE id_person_combat_card=$ipcc;";
        } else {// record doesn't exist; insert if new data added
            $dynact[$id_combat]='Yes';
            $update_head="INSERT INTO Persons_CombatCards "
                    . "(id_person, id_combat, active_authorize ";
            $update_tail="VALUES ($id_person, $id_combat, 'Yes'";
            if ($dyncard[$id_combat]!= NULL) {
                $update_head=$update_head.", card_authorize";
                $update_tail=$update_tail.", $dyncard[$id_combat]";
            }
            if ($dyndate[$id_combat]!= NULL) {
                $update_head=$update_head.", expire_authorize";
                $update_tail=$update_tail.", '$dyndate[$id_combat]'";
            }
            if ($dynnote[$id_combat]!= NULL) {
                $update_head=$update_head.", note_authorize";
                $update_tail=$update_tail.", '".sanitize_mysql($dynnote[$id_combat])."'";
            }
            $update=$update_head.") ".$update_tail.")";
        }
        if (DEBUG) {
            echo "Update query for $name_combat is:$update<p>";
        }
        echo form_subtitle("Updated $name_combat authorization: "
                          . "expires on $dyndate[$id_combat], card number $dyncard[$id_combat],"
                . " currently active is $dynact[$id_combat], and with note '"
                . sanitize_mysql($dynnote[$id_combat])."'");
        $result=update_query($cxn, $update);
        if ($result !== 1) {echo "Error updating authorization date/card number: " . mysqli_error($cxn);}
   } // Else data wasn't changed so do nothing
   $i++;
}

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
        WHERE id_person=$id_person) AS PCC
   ON AC.id_combat=PCC.ic) AS ACPCC
LEFT JOIN
   ( SELECT id_auth as ia, id_person as idp
     FROM Persons_Authorizations
     WHERE id_person=$id_person) AS AU
ON ACPCC.ip=AU.idp AND ACPCC.id_auth=AU.ia;";

if (DEBUG) {
    echo "Known authorizations: <br>$query_auths<p>";
}        
$auths = mysqli_query ($cxn, $query_auths) 
        or die ("Couldn't execute query to find known/current authorizations.");

while ($row = mysqli_fetch_assoc($auths)) {
   // Each row represents one possible authorization identified by $id_auth
   // If isset(dynidauth[$id_auth)) then the box for that authorization is ticked.
   extract($row);
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
       if ($ipcc != NULL) {
           echo " with card for $name_combat";
       }
       echo "<p>";
   }
   if ($ipcc == NULL){// No combat card info means no authorizations
       if (isset($dynidauth[$id_auth])) {
           echo form_subtitle("Cannot authorize $name_auth "
                                . "until the card information for $name_combat combat "
                                . "is completed.<p>");
       }
   } else { // We have a combat card and can now start doing stuff.
       if (($ia != NULL) && !isset($dynidauth[$id_auth])) {
           // Authorization exists in database, but box is no longer ticked
           $update = "DELETE FROM Persons_Authorizations "
                   . "WHERE id_person=$id_person "
                   . "AND id_auth = $id_auth";
           $result=update_query($cxn, $update);
           if (DEBUG) {
               echo "Deleting authorization: $update<p>";
           }
           if ($result !== 1) {
              echo "Error deleting $name_auth authorizations: ".mysqli_error($cxn);
           }
       }
       if (($ia == NULL) && isset($dynidauth[$id_auth])) {
           // Authorization needs to be added to the database
           $update="INSERT INTO Persons_Authorizations "
                   . "(id_person_auth, id_auth, id_person) "
                   . "VALUES(NULL,$id_auth,$id_person);";
           $result=update_query($cxn, $update);
           if (DEBUG) {
               echo "Adding authorization: $update<p>";
           }
           if ($result !== 1) {
              echo "Error adding $name_auth authorizations: ".mysqli_error($cxn);
           }
       }
   }
   
}

echo button_link("edit_person.php?id=$id_person", "Return to Edit Personal Page for $name_person");