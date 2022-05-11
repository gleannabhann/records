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

echo '<!-- begin marshal warrants section-->';
echo '<form class="form" action="edit_person_marshal.php" method="post">';
echo form_title("Editing Marshal's Warrants");
echo '<input type="hidden" name="id" value="'.$person["id_person"].'">';
echo '<input type="hidden" name="name_person" value="'.$person["name_person"].'">';
echo "<div class='row'>"; // start of warrants grid
echo "<div class='card border border-dark rounded'>";

$curr_id_combat=0; $i=0;

while ($row = $sth_marshals->fetch(PDO::FETCH_ASSOC)) {
    extract($row);
    $i++;
    if ($curr_id_combat!= $id_combat) {// Build for the next item in combats
        if ($curr_id_combat > 0) {
            echo "</div></div></div></div></div></div>"; //close out prev divs
            echo "<div class='row mt-2'><div class='card border border-dark rounded'>";
        }
        $curr_id_combat=$id_combat;
        $mcombat = $sth_mcombats->fetch(PDO::FETCH_ASSOC);
        echo "<div class='form-group card-header' style='background-color: #cc0000; color: white;'>";
        echo "<input type='hidden' name='dynmcombat[$id_combat]' value='$id_combat'>";
        echo "<h3 class='card-title'>$name_combat</h3></div>";
        echo "<div class='card-body'>";
        echo "<div class='row my-1'>";
        echo "<div class='col-lg-4'>";
        // create active/not active pulldown with active status selected
        // ipcc = 'id_person_combat_card'
        if ($mcombat["ipcc"] != null) {
            echo "<div class='col-xs-12 form-group'>";
            $active_status =  ['Yes', 'No' ];
            if ($mcombat["active"]==null) {
                $mcombat["active"]='No';
            }
            echo "<label class='form-label' for'dynmact[$id_combat]'>Currently Active:</label>";
            echo "<select class='form-control' name='dynmact[$id_combat]' >";
            foreach ($active_status as $value) {
                echo '<option value="'.$value.'"';
                if ($mcombat["active"]==$value) {
                    echo ' selected';
                }
                echo '>'.$value.'</option>';
            }
            echo "</select>";
            echo "</div>";
        }

        // expiration date, card number, and note fields
        echo "<div class='col-xs-12 form-group'>";
        echo "<label class='form-label' for='dynmdate[$id_combat]'>expires:<input type='date' class='date form-control' id='expire_marshal_$id_combat' "
         . "name='dynmdate[$id_combat]' value ='".$mcombat["ea"]."'></div>";
        echo "<div class='col-md-12 form-group'>"
         . "<label class='form-label' for='dynmcard[$id_combat]'>card number:</label>";
        echo "<input class='form-control' type='number' name='dynmcard[$id_combat]' value='"
         . $mcombat["cn"]."'id='card_number_$id_combat' ></div>";
        echo "<div class='col-xs-12 form-group'>"
         . "<label class='form-label' for='dynmnote[$id_combat]'>Note:</label>"
         . "<textarea class='form-control' name='dynmnote[$id_combat]' rows='4'>".$mcombat["note"]."</textarea>"
         . "</div>";
        echo "</div><!-- end of fields column -->";
        echo "<div class='col-lg-8'>"
         . "<div class='row mx-1'>";
    }

    // for all other entries in the $sth_marshals row, add a column with name of
    // type and a checkbox properly checked or not checked as needed
    // TODO when we do Bootstrap 5, change these to grid cards using
    // .row-cols-sz-* classes (under "Grid Cards" in the documentation)
    echo "<div class='col-xs-6 col-sm-4 col-md-3 form-group'>"
    . "<div class='card m-0 d-flex h-100 align-items-stretch text-center'>"
    . "<div class='card-header'><label class='col-form-label form-check-label' for='dynmidauth[$id_marshal]'>$name_marshal</label></div>"
    . "<div class='card-body align-middle'><input class='col form-check'"
    . " type='checkbox' name='dynmidauth[$id_marshal]' value='1'";
    if ($id_person_marshal!="") {
        echo " checked ";
    }
    echo "></div></div></div><!-- end of $name_marshal col -->";
}

echo '</div></div></div></div></div></div>';
echo '<input type="submit" value="Update Marshals Warrants">';
echo "</form>";
echo "<!-- end marshal warrants section -->";
