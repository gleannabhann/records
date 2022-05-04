<?php
// This is the main page for reports.  It checks for permissions and then
// loads the report_sub_<type>.php file.  We use separate files since different
// fields might include different secondary parameters.
// These sub files contain forms that are handled by report_<type>.php



if (!permissions("Herald")>=1) { // User lacks the right permissions
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') { // this page wasn't reached through a form submission
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}
    
// note: db connection now established in header.php and header_main.php
// so it should be available to this document

// Generate the report.

// Build the query based on the parameters: this will be a massive if statement.
$report=$_POST["id_report"];
switch ($report) {
    case "1": // Obsidian report
        $report_name = "List of all Awards awarded";
        $filename = "data";
        $qshow = "SELECT concat('<a href=''edit_person.php?id=',Persons.id_person,'''>',name_person,'</a>') "
                    . "as 'SCA Name', ";
        $qfile = "SELECT name_person as 'SCA Name', ";
        $query = "   name_award as Award, date_award as 'Date Awarded',
                    name_group as 'Group', name_kingdom as Kingdom 
                    from Persons, Awards, Groups, Kingdoms, Persons_Awards
                    where Persons_Awards.id_person = Persons.id_person
                    and Persons_Awards.id_award = Awards.id_award
                    and Persons.id_group = Groups.id_group
                    and Awards.id_kingdom = Kingdoms.id_kingdom
                    order by name_person, name_award;";
        break;
    default:
        echo '<p class="error"> No report selected.</p>';
        exit_with_footer();        
}

// Display data in $data
include 'report_showtable.php';

// note: footer closes the db connection now
