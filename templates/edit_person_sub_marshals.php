<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// This is the marshal's warrant form which is handled by edit_person_marshal.php
// Since this file is included from edit_person.php, the database connection $cxn is alread live.

// This query will return a list of all known marshal's warrants, 
// with the person's data filled in if known and NULL otherwise
$query_comb = "SELECT id_combat, name_combat, cn, ea, ipcc, note, active "
        . "FROM Combat LEFT JOIN"
        . "(SELECT  id_person_combat_card as ipcc, card_marshal as cn, "
        . "expire_marshal as ea, id_combat as ic, active_marshal as active, "
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
        . "(SELECT id_marshal as ia, id_person_marshal "
        . "FROM Persons_Marshals where id_person=$id_person) AS PA "
        . "on AC.id_marshal = PA.ia "
        . "order by name_combat, name_marshal";
if (DEBUG) {
    echo "Per Category known facts:<br>$query_comb<p>";
    echo "Known Marshal's Warrants: <br>$query_marshals<p>";
}        
$marshals = mysqli_query ($cxn, $query_marshals) 
        or die ("Couldn't execute query to find known/current marshal's warrants.");
$mcombats = mysqli_query ($cxn, $query_comb) 
        or die ("Couldn't execute query to find known/current date/card numbers.");
        
echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
echo '<form action="edit_person_marshal.php" method="post">';
echo form_title("Editing Marshal's Warrants");
echo '<input type="hidden" name="id" value="'.$person["id_person"].'">';
echo '<input type="hidden" name="name_person" value="'.$person["name_person"].'">';
echo "<table class='table table-condensed table-bordered'>";
//echo "<tr><th></th><th>Expiry Date</th><th>Card Number</th></tr>";
$curr_id_combat=0; $i=0;
while ($row = mysqli_fetch_assoc($marshals)){
    extract($row); $i++;
    if ($curr_id_combat!= $id_combat) {// Build for the next item in combats
        if ($curr_id_combat > 0) {
            echo "</tr>";
        }
        $curr_id_combat=$id_combat;
        $mcombat = mysqli_fetch_assoc($mcombats);
        echo "<input type='hidden' name='dynmcombat[]' value='$id_combat'>";
        echo "<tr><td class='text-center' width='25%'><strong>$name_combat</strong><br>";
        if ($mcombat["ipcc"] != NULL) {
            $active_status = array ('Yes', 'No' );
            if ($mcombat["active"]==NULL) { $mcombat["active"]='No';}
            echo "Currently Active: <select name='dynmact[$id_combat]' >";
            foreach ($active_status as $value ) {
                echo '<option value="'.$value.'"';
                if ($mcombat["active"]==$value) { echo ' selected'; }
                echo '>'.$value.'</option>';
            }
            echo "</select></br>";
        }
        echo "expires:<input type='date' class='date' id='expire_marshal_$id_combat' "
                . "name='dynmdate[$id_combat]' value ='".$mcombat["ea"]."'><br>"
                . "card number:<input type='number' name='dynmcard[$id_combat]' value='"
                . $mcombat["cn"]."'id='card_number_$id_combat' >"
                . "Note:<br><textarea name='dynmnote[$id_combat]' rows='2' cols='10'>".$mcombat["note"]."</textarea>"
                . "</td>";
    }
    // TODO Fix alignment of checkbox so all are even with bottom
    echo "<td class='text-center'>$name_marshal"
       . "<input type='checkbox' name='dynmidauth[$id_marshal]' value='1'";
    if ($id_person_marshal!=""){
        echo " checked ";
    }
    echo "></td>";
}
echo "</tr>";
echo "</table>";
echo '<input type="submit" value="Update Marshals Warrants">';
echo "</form>";
echo "</div></div>";
?>