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

// Build the query based on the parameters

// Query the database

// If requested as a file, build the file, else display in a table with sortable columns

mysqli_close ($cxn); // Close the db connection