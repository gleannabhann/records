<?php
// Assumption: this is only loaded from reports.php so don't need to access check again
// Note: cxn is live.

$query = "SELECT id_combat, name_combat FROM Combat";
$combats = mysqli_query ($cxn, $query) 
        or die ("Couldn't execute query to get list of combats.");
$query = "SELECT id_auth, id_combat, name_auth FROM Authorizations"; 
$auths = mysqli_query ($cxn, $query) 
        or die ("Couldn't execute query to get list of Authorizations.");
echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
echo '<form action="report_marshal.php" method="post">';
echo "<table class='table table-condensed table-bordered'>";
echo form_title("Please select a Marshal's report");
echo '<tr><td class="text-right">Report:</td>';
    echo '<td><select name="id_report" >';
    echo '<option value="1">List of all Active Fighters for Choice of Combat</option>';
    echo '<option value="2">List of all Active Marshals for Choice of Combat</option>';
    echo '</td></tr>';
echo '<tr><td class="text-right">Type of Combat:</td>';
    echo '<td><select name="id_combat" >';
    while ($combat=  mysqli_fetch_assoc($combats)){
        extract($combat);
        echo "<option value='$id_combat'>"
                . "$name_combat</option>";
        // Remember you will need to use explode function to separate combat
        //    name and id
    }
echo '<tr><td class="text-right">Type of Authorization:<br></td>';
    echo '<td><select name="id_auth" >';
    echo '<option value="-1">All Authorizations for Chosen Combat</option>';
    while ($auth =  mysqli_fetch_assoc($auths)){
        // can't extract $auth; name conflict with id_combat
        echo "<option value='".$auth["id_auth"]."'>"
                . $auth["name_auth"]."</option>";
        // Remember you will need to use explode function to separate combat
        //    name and id
    }    
    echo '</select></td></tr>';
echo '</table>';
echo '<input type="submit" value="Get Report">';
echo "</form>";
echo "</div></div>";