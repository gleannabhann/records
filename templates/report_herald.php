<?php
// This is the main page for reports.  It checks for permissions and then
// loads the report_sub_<type>.php file.  We use separate files since different
// fields might include different secondary parameters.
// These sub files contain forms that are handled by report_<type>.php



if (!permissions("Herald")>=3) { // User lacks the right permissions
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
switch ($report) {
    case "1": // Obsidian report
        $report_name = "List of all Awards awarded";
        $query = "select name_person as Name, name_award as Award, date_award as 'Date Awarded',
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
// Query the database
if (DEBUG) {
    echo "Report query is: $query<p>";
}
$data = mysqli_query ($cxn, $query) 
        or die ("Couldn't execute query to build report.");

// If requested as a file, build the file, else display in a table with sortable columns
if (isset($_POST["get_file"])) {
    // build file and offer for download
} else {
    // Display data

    
    echo form_title($report_name);
    $fields = mysqli_fetch_fields($data);
//    echo "<table class='table table-condensed table-bordered'>";
    echo '<table class="sortable table table-condensed table-bordered">';
    echo '<thead>';
        foreach ($fields as $field) {
            echo '<th>'.$field->name.'</th>';
        }
        echo '</thead>';
    while ($row = mysqli_fetch_assoc($data)) {
        echo '<tr>';
        foreach ($row as $field) {
            echo '<td>'.$field.'</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
}
mysqli_close ($cxn); // Close the db connection