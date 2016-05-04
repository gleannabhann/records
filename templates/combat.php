<?php

/* Combat Landing page
 * Will begin by listing links to various marshal's warrants lists.
 * Will expand later
 */

// Build the Links to the Marshal list pages depending on what is in the database
/* connect to the database */
$cxn = open_db_browse();
echo "<div class='row'><div class='col-md-6 col-md-offset-3'>";
///////////////////////////////////////////////////////////////////////////////
// Main portion of the page
///////////////////////////////////////////////////////////////////////////////
echo "<div class='center-block'>";
echo form_title("Active Marshals for each Combat Category:");
$query="SELECT id_combat, name_combat from Combat";
if (DEBUG) {echo "Combat query: $query<p>";}
$result = mysqli_query ($cxn, $query) 
    or die ("Couldn't execute query to find types of combat");

echo "<div class='list-group'><ul type='none'>"; 
while ($row = mysqli_fetch_assoc($result)){
    extract($row);
    echo "<li><a href='list_marshals.php?id=$id_combat'>$name_combat: Active Marshals</a></li>";
}
echo "</ul>";
echo "<p>";
///////////////////////////////////////////////////////////////////////////////
// Form allowing fighter to print his/her authorization card
///////////////////////////////////////////////////////////////////////////////
echo form_title("Show Combat Authorization Card");
echo '<form action="/public/combat_auth.php" method="post">';
echo "<table class='table table-condensed table-bordered'>";
echo '<tr><td class="text-right">Combat Type:</td><td> <select name="id_combat" >';
    $query="SELECT id_combat, name_combat FROM Combat ORDER BY name_combat";
    // Build up the drop down list
    if (DEBUG) {echo "Combat query: $query<p>";}
    $result = mysqli_query ($cxn, $query) 
        or die ("Couldn't execute query to find types of combat");
    while ($row = mysqli_fetch_assoc($result)){
        extract($row);
        echo '<option value="'.$row["id_combat"].'"';
        echo '>'.$row["name_combat"].'</option>';
    }
echo '</select></td></tr>';
echo  '</tr>
          <td class="text-right">SCA Membership #:<br>(required)</td>
          <td><input type="number" name="mem_num" min="1" step="1"></td>
      </tr>';
echo '</table>';
echo '<input type="submit" value="Show Card" class="btn btn-primary">';
echo '</form>';
echo "</div>"; // Center block
echo "</div>"; // Center block
echo "</div><!-- ./col-md-6 -->";

///////////////////////////////////////////////////////////////////////////////
// News column on right
///////////////////////////////////////////////////////////////////////////////
echo '<div class="col-md-3 well">';
echo form_subtitle("Upcoming Expirations");
$query = "SELECT Persons.id_person, name_person, expire_authorize, PCC.id_combat, name_combat "
        . "FROM Persons, Persons_CombatCards as PCC, Combat "
        . "WHERE Persons.id_person=PCC.id_person "
        . "AND PCC.id_combat=Combat.id_combat "
        . "AND active_authorize='Yes' "
        . "AND expire_authorize>= now() "
        . "ORDER BY expire_authorize "
        . "LIMIT 20;";

        
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute query");
while ($row = mysqli_fetch_assoc($result)) {
    extract($row);
    if (permissions("Marshal")>=3) {
        echo "<li><a href='edit_person.php?id=$id_person'>";
    } else {
        echo "<li><a href='person.php?id=$id_person'>";
    }
    echo "$name_person</a> has an expiring authorization for $name_combat on $expire_authorize"
            . "</li>";
}

