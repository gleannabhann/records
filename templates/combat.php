<?php

/* Combat Landing page
 * Will begin by listing links to various marshal's warrants lists.
 * Will expand later
 */

// Build the Links to the Marshal list pages depending on what is in the database
/* connect to the database */
$cxn = open_db_browse();

$query="SELECT id_combat, name_combat from Combat";
if (DEBUG) {echo "Combat query: $query<p>";}
$result = mysqli_query ($cxn, $query) 
    or die ("Couldn't execute query to find types of combat");

echo "<div class='list-group'><ul type='none'>"; 
while ($row = mysqli_fetch_assoc($result)){
    extract($row);
    echo "<li><a href='list_marshals.php?id=$id_combat'>$name_combat: Active Marshals</a></li>";
}
echo "</ul></div>";