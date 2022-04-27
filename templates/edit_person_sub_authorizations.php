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
        . "WHERE id_person=:id_person) AS PA "
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
        . "FROM Persons_Authorizations where id_person=:id_person) AS PA "
        . "on AC.id_auth = PA.ia "
        . "order by name_combat,name_auth";
$data = ['id_person' => $id_person];
if (DEBUG) {
    echo "Per Category known facts:<br>$query_comb<p>";
    echo "Known authorizations: <br>$query_auths<p>";
}        
$sth_auths = $cxn->prepare($query_auths);
$sth_auths->execute($data);

$sth_combats = $cxn->prepare($query_comb);
$sth_combats->execute($data);
        
echo "<div class='row justify-content-center'><div class='col-md-8'>";
echo "<form action='edit_person_authorization.php' method='post'>";
echo form_title("Editing Authorizations");
echo '<input type="hidden" name="id" value="'.$person["id_person"].'">';
echo '<input type="hidden" name="name_person" value="'.$person["name_person"].'">';
echo "<div class='card my-3 border border-dark rounded'><!-- begin first combat type row -->"; // start of auths grid
//echo "<tr><th></th><th>Expiry Date</th><th>Card Number</th></tr>";
$curr_id_combat=0; $i=0;
while ($row = $sth_auths->fetch(PDO::FETCH_ASSOC)){
    extract($row); $i++;
    if ($curr_id_combat!= $id_combat) {// Build for the next item in combats
      if ($curr_id_combat > 0) {
        echo "</div><!-- end auth-type card body -->";
        echo "</div><!-- end auth-type card -->";
        echo "</div><!-- end auth-type cards column -->";
        echo "</div><!-- end row inside main card body -->";
          echo "</div><!-- end combat type card -->";
          echo "<div class='card my-3 border border-dark rounded'><!-- begin next combat-type card -->";
        }
        $curr_id_combat=$id_combat;
        $combat = $sth_combats->fetch(PDO::FETCH_ASSOC);
        echo "<div class='form-group'>";
        echo "<input type='hidden' name='dyncombat[$id_combat]' value='$id_combat'>";
        echo "<h3 class='card-header'>$name_combat</h3><br>";
        echo "</div>";
        echo "<div class='card-body form-group'>";
        echo "<div class='row'>";
        echo "<div class='col-4'><!-- begin fields col -->";
        if ($combat["ipcc"] != NULL) {
            echo "<div class='row mx-1'>";
            $active_status =  ['Yes', 'No' ];
            if ($combat["active"]==NULL) { $combat["active"]='No';}
            echo "<div class='col'><label for='dynact[$id_combat]'>Currently Active:</label></div>"
               . "<div class='col'> <select class='form-control' name='dynact[$id_combat]' >";
            foreach ($active_status as $value ) {
                echo '<option value="'.$value.'"';
                if ($combat["active"]==$value) { echo ' selected'; }
                echo '>'.$value.'</option>';
            }
            echo "</select></div></div>";
        }
        // expiration date, card number, and note fields
        echo "<div class='row mx-1'>";
        echo "<label for='dyndate[$id_combat]'>expires:</label><input type='date' class='date form-control' id='expire_auth_$id_combat' "
                . "name='dyndate[$id_combat]' value ='".$combat["ea"]."'></div><div class='row mx-1'>"
                . "<label for='dyncard[$id_combat]'>card number:</label><input class='form-control' type='number' name='dyncard[$id_combat]' value='"
                . $combat["cn"]."'id='card_number_$id_combat' ></div><div class='row mx-1'>"
                . "<label for='dynnote[$id_combat]'>Note:</label><br><textarea class='form-control' name='dynnote[$id_combat]' rows='2' cols='10'>".$combat["note"]."</textarea>"
                . "</div>";
        echo "</div><!-- end of fields column -->";
        echo "<div class='col-8'><div class='row mx-1'><!-- begin auths cards -->";
    }
    // for all other entries in the row, add a column with name of type and
    // a checkbox properly checked or not checked as needed
    echo "<div class='col-3 form-group'><!-- begin single card column -->";
    echo "<!-- begin $name_auth card --><div class='card m-0 d-flex h-100 align-items-stretch'>";
    echo "<div class='card-title'><label class='form-check-label' for='dynidauth[$id_auth]'><p class='text-center'>$name_auth</p></label></div>"
       . "<div class='card-body text-center align-middle'><input type='checkbox' name='dynidauth[$id_auth]' value='1'";
    if ($id_person_auth!=""){
        echo " checked ";
    }
    echo "></div><!--end of card-body--></div><!-- end of $name_auth card -->"
      . "</div><!-- end single card column -->";
}
echo "</div><!-- end of final auth-type card body -->";
echo "</div><!-- end of final auth-type card -->";
echo "</div><!-- end of final auth cards column -->";
echo "</div><!-- end of row inside final combat-type card -->";
echo "</div><!-- end final combat type card -->";
echo '<input type="submit" value="Update Authorizations">';
echo "</form>";
echo "</div></div>";
?>
