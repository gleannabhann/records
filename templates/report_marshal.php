<?php
// This is the main page for reports.  It checks for permissions and then
// loads the report_sub_<type>.php file.  We use separate files since different
// fields might include different secondary parameters.
// These sub files contain forms that are handled by report_<type>.php



if (!permissions("Marshal")>=1) { // User lacks the right permissions
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') { // this page wasn't reached through a form submission
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}
    
$cxn = open_db_browse(); // Open the db connection which is now live for the subforms
// Generate the report.

// Build the query based on the parameters: this will be a massive if statement.
$report=$_POST["id_report"];
$combat=$_POST["id_combat"];
switch ($report) {
    case "1": 
        $report_name = "List of all Active Fighters";
        $query = "SELECT concat('<a href=''edit_person.php?id=',Persons.id_person,'''>',name_person,'</a>') "
                    . "as 'SCA Name', "
                . "name_mundane_person as 'Legal Name', name_group as 'Group', "
                . "membership_person as 'Mem #', membership_expire_person as 'Mem Date',"
                . "waiver_person as 'Combat Waiver', card_authorize as 'Fighter Card',"
                . "expire_authorize as 'Expire' "
                . "FROM Persons, Persons_CombatCards, Groups "
                . "WHERE Persons_CombatCards.id_combat=$combat "
                . "AND Persons_CombatCards.active_authorize='Yes' "
                . "AND Persons.id_person=Persons_CombatCards.id_person "
                . "AND Persons.id_group=Groups.id_group";
        break;
    case "2": 
        $report_name = "List of all Active Marshals";
        $query = "SELECT concat('<a href=''edit_person.php?id=',Persons.id_person,'''>',name_person,'</a>') "
                    . "as 'SCA Name', "
                . "name_mundane_person as 'Legal Name', name_group as 'Group', "
                . "membership_person as 'Mem #', membership_expire_person as 'Mem Date',"
                . "waiver_person as 'Combat Waiver', card_marshal as 'Marshal Card',"
                . "expire_marshal as 'Expire' "
                . "FROM Persons, Persons_CombatCards, Groups "
                . "WHERE Persons_CombatCards.id_combat=$combat "
                . "AND Persons_CombatCards.active_marshal='Yes' "
                . "AND Persons.id_person=Persons_CombatCards.id_person "
                . "AND Persons.id_group=Groups.id_group";
        break;
    default:
        echo '<p class="error"> No report selected.</p>';
        exit_with_footer();        
}
// Query the database
if (DEBUG) {
    echo "Report query is: $query<p>";
}
// Query the database
$data = mysqli_query ($cxn, $query) 
        or die ("Couldn't execute query to build report.");
// If requested as a file, build the file, else display in a table with sortable columns
if (isset($_POST["get_file"])) {
    // build file and offer for download
} else {
    // Display data in $data
    include 'report_showtable.php';
}
mysqli_close ($cxn); // Close the db connection