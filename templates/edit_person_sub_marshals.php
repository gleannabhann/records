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
        . "WHERE id_person=:id_person) AS PA "
        . "ON Combat.id_combat = PA.ic ORDER BY name_combat";

$query_marshals = "SELECT * FROM "
        . "(SELECT id_marshal, name_marshal, Combat.id_combat, name_combat "
        . "FROM Marshals, Combat "
        . "WHERE Marshals.id_combat = Combat.id_combat "
        . "ORDER BY name_combat, name_marshal) AS AC "
        . "LEFT JOIN "
        . "(SELECT id_marshal as ia, id_person_marshal "
        . "FROM Persons_Marshals where id_person=:id_person) AS PA "
        . "on AC.id_marshal = PA.ia "
        . "order by name_combat, name_marshal";
$data = ['id_person' => $id_person];
if (DEBUG) {
    echo "Per Category known facts:<br>$query_comb<p>";
    echo "Known Marshal's Warrants: <br>$query_marshals<p>";
}
$sth_marshals = $cxn->prepare($query_marshals);
$sth_marshals->execute($data);

$sth_mcombats = $cxn->prepare($query_comb);
$sth_mcombats->execute($data);
        
echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
echo '<form action="edit_person_marshal.php" method="post">';
echo form_title("Editing Marshal's Warrants");
echo '<input type="hidden" name="id" value="'.$person["id_person"].'">';
echo '<input type="hidden" name="name_person" value="'.$person["name_person"].'">';
echo "<div class='row'>"; // start of warrants grid
$curr_id_combat=0; $i=0;
// echo "<div class='col'><!-- begin fields col -->";
while ($row = $sth_marshals->fetch(PDO::FETCH_ASSOC)){
  extract($row); $i++;
  if ($curr_id_combat!= $id_combat) {// Build for the next item in combats
      if ($id_combat > 0) { echo "</div><div class='row'>"; }
      echo "<div class='col'><!-- begin fields col -->"; //fields column
      $curr_id_combat=$id_combat;
      $mcombat = $sth_mcombats->fetch(PDO::FETCH_ASSOC);
      echo "<div class='row'>";
      echo "<input type='hidden' name='dynmcombat[]' value='$id_combat'>";
      echo "<strong>$name_combat</strong>";
      echo "</div>";
      // create active/not active pulldown with active status selected
      // ipcc = 'id_person_combat_card'
      if ($mcombat["ipcc"] != NULL) {
          echo "<div class='row'>";
          $active_status =  ['Yes', 'No' ];
          if ($mcombat["active"]==NULL) { $mcombat["active"]='No';}
          echo "Currently Active: <select name='dynmact[$id_combat]' >";
          foreach ($active_status as $value ) {
              echo '<option value="'.$value.'"';
              if ($mcombat["active"]==$value) { echo ' selected'; }
              echo '>'.$value.'</option>';
          }
          echo "</select>";
          echo "</div>";
      }
      // build expiration date, card number, and note fields, and populate
      echo "<div class='row'>";
      echo "expires:<input type='date' class='date' id='expire_marshal_$id_combat' "
              . "name='dynmdate[$id_combat]' value ='".$mcombat["ea"]."'></div><div class='row'>"
              . "card number:<input type='number' name='dynmcard[$id_combat]' value='"
              . $mcombat["cn"]."'id='card_number_$id_combat' ></div><div class='row'>"
              . "Note:<br><textarea name='dynmnote[$id_combat]' rows='2' cols='10'>".$mcombat["note"]."</textarea>"
              . "</div>";
      echo "</div><!-- end of fields column -->";
  }
  // for all other entries in the $sth_marshals row, add a column with name of
  // type and a checkbox properly checked or not checked as needed
  // TODO Fix alignment of checkbox so all are even with bottom
  
  echo "<div class='col'><span class='text-center align-top'>$name_marshal</span>"
    . "<span class='text-center align-middle><input class='align-middle text-center'"
    . " type='checkbox' name='dynmidauth[$id_marshal]' value='1'";
  if ($id_person_marshal!=""){
      echo " checked ";
  }
  echo "></span></div><!-- end of $name_marshal col -->";

}
echo '</div>';
echo '<input type="submit" value="Update Marshals Warrants">';
echo "</form>";
echo "</div></div>";
?>
