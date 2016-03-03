<?php
// Assumption: this is only loaded from reports.php so don't need to access check again
// Note: cxn is live.

$query = "SELECT id_combat, name_combat FROM Combat";
$combats = mysqli_query ($cxn, $query) 
        or die ("Couldn't execute query to get list of combats.");
$query = "SELECT id_auth, id_combat, name_auth FROM Authorizations"; 
$auths = mysqli_query ($cxn, $query) 
        or die ("Couldn't execute query to get list of Authorizations.");
$query = "SELECT id_marshal, id_combat, name_marshal FROM Marshals"; 
$marshals = mysqli_query ($cxn, $query) 
        or die ("Couldn't execute query to get list of Marshal''s Warrants.");
echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
echo '<form action="report_marshal.php" method="post">';
echo "<table class='table table-condensed table-bordered'>";
echo form_title("Please select a Marshal's report");
echo '<tr><td class="text-right">Report:</td>';
    echo '<td><select name="id_report" >';
    echo "<option value='7'>List of all Active Fighters with at least one Authorization for Choice of Combat</option>";
    echo '<option value="1">List of all Active Fighters for Choice of Combat</option>';
    echo '<option value="3">List of all Active Fighters for Choice of Authorization</option>';
    echo "<option value='5'>List of all Inactive Fighters for Choice of Combat</option>";
    echo '<option value="2">List of all Active Marshals for Choice of Combat</option>';
    echo "<option value='4'>List of all Active Marshals for Choice of Marshal's Warrant</option>";
    echo "<option value='6'>List of all Inactive Marshals for Choice of Combat</option>";
    echo '</td></tr>';
echo '<tr><td class="text-right">Type of Combat:</td>';
    echo '<td><select name="id_combat" >';
    while ($combat=  mysqli_fetch_assoc($combats)){
        extract($combat);
        echo "<option value='$id_combat|$name_combat'>"
                . "$name_combat</option>";
        // Remember you will need to use explode function to separate combat
        //    name and id
    }
echo '<tr><td class="text-right">Type of Authorization:<br></td>';
    echo '<td><select name="id_auth" >';
    echo '<option value="-1|All Authorizations for Chosen Combat">All Authorizations for Chosen Combat</option>';
    while ($auth =  mysqli_fetch_assoc($auths)){
        // can't extract $auth; name conflict with id_combat
        echo "<option value='".$auth["id_auth"]."|".$auth["name_auth"]."'>"
                . $auth["name_auth"]."</option>";
        // Remember you will need to use explode function to separate combat
        //    name and id
    }    
    echo '</select></td></tr>';
    echo "<tr><td class='text-right'>Type of Marshal's Warrant:<br></td>";
    echo '<td><select name="id_marshal" >';
    echo "<option value='-1|All Marshal's Warrants for Chosen Combat>All Marshal's Warrants for Chosen Combat</option>";
    while ($marshal =  mysqli_fetch_assoc($marshals)){
        // can't extract $auth; name conflict with id_combat
        echo "<option value='".$marshal["id_marshal"]."|".$marshal["name_marshal"]."'>"
                . $marshal["name_marshal"]."</option>";
        // Remember you will need to use explode function to separate combat
        //    name and id
    }    
 //echo '<tr><td>Download as a file?</td>';
//    echo '<td><input type="checkbox" name="get_file" value="1">';
//    echo '</td></tr>';   echo '</select></td></tr>';
    
echo '</table>';
echo '<input type="submit" value="Get Report">';
echo "</form>";
echo "</div></div>";