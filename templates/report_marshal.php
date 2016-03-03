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
$report =$_POST["id_report"];
$combat =explode('|',$_POST["id_combat"]);
$auth   =explode('|',$_POST["id_auth"]);
$marshal=explode('|',$_POST["id_marshal"]);
switch ($report) {
    case "1": // All active fighters of given combat type
        $report_name = "List of all Active Fighters for $combat[1]";
        $filename = "data";
        $qshow = "SELECT concat('<a href=''edit_person.php?id=',Persons.id_person,'''>',name_person,'</a>') "
                    . "as 'SCA Name', ";
        $qfile = "SELECT name_person as 'SCA Name', ";
        $query = "name_mundane_person as 'Legal Name', name_group as 'Group', "
                . "membership_person as 'Mem #', membership_expire_person as 'Mem Date',"
                . "waiver_person as 'Combat Waiver', card_authorize as 'Fighter Card',"
                . "expire_authorize as 'Expire' "
                . "FROM Persons, Persons_CombatCards, Groups "
                . "WHERE Persons_CombatCards.id_combat=$combat[0] "
                . "AND Persons_CombatCards.active_authorize='Yes' "
                . "AND Persons.id_person=Persons_CombatCards.id_person "
                . "AND Persons.id_group=Groups.id_group "
                . "ORDER BY Persons.name_person";
        break;
    case "2": // All active marshals of given combat type
        $report_name = "List of all Active Marshals for $combat[1]";
        $filename = "data";
        $qshow = "SELECT concat('<a href=''edit_person.php?id=',Persons.id_person,'''>',name_person,'</a>') "
                    . "as 'SCA Name', ";
        $qfile = "SELECT name_person as 'SCA Name', ";
        $query = "name_mundane_person as 'Legal Name', name_group as 'Group', "
                . "membership_person as 'Mem #', membership_expire_person as 'Mem Date',"
                . "waiver_person as 'Combat Waiver', card_marshal as 'Marshal Card',"
                . "expire_marshal as 'Expire' "
                . "FROM Persons, Persons_CombatCards, Groups "
                . "WHERE Persons_CombatCards.id_combat=$combat[0] "
                . "AND Persons_CombatCards.active_marshal='Yes' "
                . "AND Persons.id_person=Persons_CombatCards.id_person "
                . "AND Persons.id_group=Groups.id_group "
                . "ORDER BY Persons.name_person";
        break;
    case "3": // All fighters with specific authorization
        $report_name = "List of all Active Fighters for $auth[1]";
        $filename = "data";
        $qshow = "SELECT concat('<a href=''edit_person.php?id=',Persons.id_person,'''>',name_person,'</a>') "
                    . "as 'SCA Name', ";
        $qfile = "SELECT name_person as 'SCA Name', ";        
        $query = "name_mundane_person as 'Legal Name', name_group as 'Group', "
                . "membership_person as 'Mem #', membership_expire_person as 'Mem Date',"
                . "waiver_person as 'Combat Waiver', card_authorize as 'Fighter Card',"
                . "expire_authorize as 'Expire' "
                . "FROM Persons, Persons_CombatCards, Groups, Persons_Authorizations, Authorizations "
                . "WHERE Persons_CombatCards.id_combat=$combat[0] "
                . "AND Persons_CombatCards.active_authorize='Yes' "
                . "AND Persons.id_person=Persons_CombatCards.id_person "
                . "AND Persons.id_person=Persons_Authorizations.id_Person "
                . "AND Persons_Authorizations.id_auth=Authorizations.id_auth "
                . "AND Authorizations.id_combat=$combat[0] ";
        if ($auth[0] > 0) {$query=$query."AND Persons_Authorizations.id_auth=$auth[0] "; }
        $query=$query."AND Persons.id_group=Groups.id_group "
                . "ORDER BY Persons.name_person";
        break;
    case "4": // All marshals of specific warrant type
        $report_name = "List of all Active Marshals for $marshal[1]";
        $filename = "data"; 
        $qshow = "SELECT concat('<a href=''edit_person.php?id=',Persons.id_person,'''>',name_person,'</a>') "
                    . "as 'SCA Name', ";
        $qfile = "SELECT name_person as 'SCA Name', ";
        $query = "name_mundane_person as 'Legal Name', name_group as 'Group', "
                . "membership_person as 'Mem #', membership_expire_person as 'Mem Date',"
                . "waiver_person as 'Combat Waiver', card_marshal as 'Marshal Card',"
                . "Persons_CombatCards.expire_marshal as 'Expire' "
                . "FROM Persons, Persons_CombatCards, Groups, Persons_Marshals, Marshals "
                . "WHERE Persons_CombatCards.id_combat=$combat[0] "
                . "AND Persons_CombatCards.active_marshal='Yes' "
                . "AND Persons.id_person=Persons_CombatCards.id_person "
                . "AND Persons.id_person=Persons_Marshals.id_person "
                . "AND Persons_Marshals.id_marshal=Marshals.id_marshal "
                . "AND Marshals.id_combat = $combat[0] ";
        if ($marshal[0] > 0) {$query=$query."AND Persons_Marshals.id_marshal=$marshal[0] "; }
        $query=$query."AND Persons.id_group=Groups.id_group "
                . "ORDER BY Persons.name_person";
        break;       
    case "5": // All inactive fighters of given combat type
        $report_name = "List of all Inactive Fighters for $combat[1]";
        $filename = "data";
        $qshow = "SELECT concat('<a href=''edit_person.php?id=',Persons.id_person,'''>',name_person,'</a>') "
                    . "as 'SCA Name', ";
        $qfile = "SELECT name_person as 'SCA Name', ";
        $query = "name_mundane_person as 'Legal Name', name_group as 'Group', "
                . "membership_person as 'Mem #', membership_expire_person as 'Mem Date',"
                . "waiver_person as 'Combat Waiver', card_authorize as 'Fighter Card',"
                . "expire_authorize as 'Expire' "
                . "FROM Persons, Persons_CombatCards, Groups "
                . "WHERE Persons_CombatCards.id_combat=$combat[0] "
                . "AND Persons_CombatCards.active_authorize='No' "
                . "AND Persons.id_person=Persons_CombatCards.id_person "
                . "AND Persons.id_group=Groups.id_group "
                . "ORDER BY Persons.name_person";
        break;
    case "6": // All inactive marshals of given combat type
              // Note: to be an inactive marshal you need to be set inactive *and* have at 
              //       least one entry in the Persons_Marshals table for given combat type
        $report_name = "List of all Inactive Marshals for $combat[1]";
        $filename = "data";
        $qshow = "SELECT concat('<a href=''edit_person.php?id=',Persons.id_person,'''>',name_person,'</a>') "
                    . "as 'SCA Name', ";
        $qfile = "SELECT name_person as 'SCA Name', ";
        $query = "name_mundane_person as 'Legal Name', name_group as 'Group', "
                . "membership_person as 'Mem #', membership_expire_person as 'Mem Date', "
                . "waiver_person as 'Combat Waiver', card_marshal as 'Marshal Card', "
                . "expire_marshal as 'Expire' "
                . "FROM Persons, Persons_CombatCards, Groups, Persons_Marshals, Marshals "
                . "WHERE Persons_CombatCards.id_combat=$combat[0] "
                . "AND Persons_CombatCards.active_marshal='No' "
                . "AND Persons.id_person=Persons_CombatCards.id_person "
                . "AND Persons.id_group=Groups.id_group "
                . "AND Persons.id_person=Persons_Marshals.id_person "
                . "AND Persons_Marshals.id_marshal = Marshals.id_marshal "
                . "AND Marshals.id_combat = $combat[0] "
                . "ORDER BY Persons.name_person "
                . "GROUP BY Persons.id_person";
        break;
    case "7": // All fighters with at least one authorization of given combat type
        // This query has to be built dynamically based on Authorizations
        $report_name = "List of all Fighters with at least one Valid Authorization for $combat[1]";
        $filename = "data";
        $q_auth = "SELECT id_auth, name_auth FROM Authorizations "
            . "WHERE Authorizations.id_combat=$combat[0]";
        if (DEBUG) {echo "Authorizations query: $q_auth<p>";}
        $auths = mysqli_query ($cxn, $q_auth) 
            or die ("Couldn't execute query to find authorizations to build report.");
        $qshow = "SELECT concat('<a href=''edit_person.php?id=',PCC.id_person,'''>',name_person,'</a>') "
                    . "as 'SCA Name', ";
        $qfile = "SELECT name_person as 'SCA Name', ";

        $q_head = "PCC.card_authorize as 'card number', "
                . "PCC.expire_authorize as 'expiry date' ";
        $q_body = "FROM 
           (SELECT Persons.id_person, id_person_combat_card,  name_person, card_authorize, 
                   expire_authorize  
            FROM Persons_CombatCards, Persons 
            WHERE Persons_CombatCards.id_person=Persons.id_person 
            AND Persons_CombatCards.expire_authorize >= curdate() 
            AND id_combat=$combat[0]) AS PCC
           LEFT JOIN
           (SELECT COUNT(*) as num_count, id_person 
            FROM Persons_Authorizations, Authorizations
            WHERE Persons_Authorizations.id_auth=Authorizations.id_auth
            AND Authorizations.id_combat=$combat[0]
            GROUP BY id_person) AS PCount
            ON PCount.id_person = PCC.id_person ";
        while ($auth=  mysqli_fetch_assoc($auths)) {
            extract($auth);
            $q_head = $q_head . ", if (PA$id_auth.id_person IS NULL,'No', 'Yes') as '$name_auth' ";
            $q_body = $q_body . 
                    "LEFT JOIN 
                       (SELECT id_person
                        FROM Persons_Authorizations
                        WHERE Persons_Authorizations.id_auth=$id_auth) AS PA$id_auth
                        ON PA$id_auth.id_person=PCC.id_person ";
        }
        $query = $q_head . $q_body . "WHERE num_count is not NULL ORDER BY name_person";
        break;
    default:
        echo '<p class="error"> No report selected.</p>';
        exit_with_footer();        
}

// Display data in $data (includes possibility to download)
include 'report_showtable.php';

mysqli_close ($cxn); // Close the db connection