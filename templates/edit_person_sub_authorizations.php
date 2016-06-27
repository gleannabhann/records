<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// This is the authorization form which is handled by edit_person_authorization.php
// Since this file is included from edit_person.php, the database connection $cxn is alread live.

// This query will return a list of all known authorizations, 
// with the person's data filled in if known and NULL otherwise
$query_comb = "SELECT Combat.id_combat, name_combat, cn, ea, ipcc, note, active "
        . "FROM Combat, Authorizations LEFT JOIN "
        . "(SELECT  id_person_combat_card as ipcc, card_authorize as cn, "
        . "expire_authorize as ea, id_combat as ic, note_authorize as note, "
        . "active_authorize as active "
        . "FROM  Persons_CombatCards "
        . "WHERE id_person=$id_person) AS PA "
        . "ON id_combat = PA.ic "
        . "WHERE Combat.id_combat=Authorizations.id_combat "
        . "GROUP BY Combat.id_combat "
        . "ORDER BY name_combat ";
$query_auths = "SELECT * FROM "
        . "(SELECT id_auth, name_auth, Combat.id_combat, name_combat "
        . "FROM Authorizations, Combat "
        . "WHERE Authorizations.id_combat = Combat.id_combat "
        . "ORDER BY name_combat, name_auth) AS AC "
        . "LEFT JOIN "
        . "(SELECT id_auth as ia, id_person_auth "
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
        
echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
echo '<form action="edit_person_authorization.php" method="post">';
echo form_title("Editing Authorizations");
echo '<input type="hidden" name="id" value="'.$person["id_person"].'">';
echo '<input type="hidden" name="name_person" value="'.$person["name_person"].'">';
echo "<table class='table table-condensed table-bordered'>";
//echo "<tr><th></th><th>Expiry Date</th><th>Card Number</th></tr>";
$curr_id_combat=0; $i=0;
while ($row = mysqli_fetch_assoc($auths)){
    extract($row); $i++;
    if ($curr_id_combat!= $id_combat) {// Build for the next item in combats
        if ($curr_id_combat > 0) {
            echo "</tr>";
        }
        $curr_id_combat=$id_combat;
        $combat = mysqli_fetch_assoc($combats);
        echo "<input type='hidden' name='dyncombat[$id_combat]' value='$id_combat'>";
        echo "<tr><td class='text-center' width='25%'><strong>$name_combat</strong><br>";
        
        if ($combat["ipcc"] != NULL) {
            $active_status = array ('Yes', 'No' );
            if ($combat["active"]==NULL) { $combat["active"]='No';}
            echo "Currently Active: <select name='dynact[$id_combat]' >";
            foreach ($active_status as $value ) {
                echo '<option value="'.$value.'"';
                if ($combat["active"]==$value) { echo ' selected'; }
                echo '>'.$value.'</option>';
            }
            echo "</select></br>";
        }
        echo "expires:<input type='date' class='date' id='expire_auth_$id_combat' "
                . "name='dyndate[$id_combat]' value ='".$combat["ea"]."'><br>"
                . "card number:<input type='number' name='dyncard[$id_combat]' value='"
                . $combat["cn"]."'id='card_number_$id_combat' >"
                . "Note:<br><textarea name='dynnote[$id_combat]' rows='2' cols='10'>".$combat["note"]."</textarea>"
                . "</td>";
    }
    echo "<td class='text-center'>$name_auth"
       . "<input type='checkbox' name='dynidauth[$id_auth]' value='1'";
    if ($id_person_auth!=""){
        echo " checked ";
    }
    echo "></td>";
}
echo "</tr>";
echo "</table>";
echo '<input type="submit" value="Update Authorizations">';
echo "</form>";
echo "</div></div>";
?>