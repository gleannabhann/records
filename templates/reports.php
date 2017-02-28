<?php
// This is the main page for reports.  It checks for permissions and then
// loads the report_sub_<type>.php file.  We use separate files since different
// fields might include different secondary parameters.
// These sub files contain forms that are handled by report_<type>.php



if ((!permissions("Herald")>=1) 
        && (!permissions("Marshal")>=1) 
        && (!permissions("Sites")>=1)) {
    echo '<p class="error"> This page has been accessed in error...</p>';
    exit_with_footer();
}
$cxn = open_db_browse(); // Open the db connection which is now live for the subforms

// If a herald is logged in, they see the herald reports.
if (permissions("Herald")>= 1){
   include 'report_sub_herald.php';
}

if (permissions("Marshal")>= 1){
   include 'report_sub_marshal.php';
}

if (permissions("Sites")>= 1){
   include 'report_sub_sites.php';
}

mysqli_close ($cxn); /* close the db connection */