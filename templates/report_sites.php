<?php
// This is the main page for reports.  It checks for permissions and then
// loads the report_sub_<type>.php file.  We use separate files since different
// fields might include different secondary parameters.
// These sub files contain forms that are handled by report_<type>.php



if (!permissions("Sites")>=1) { // User lacks the right permissions
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') { // this page wasn't reached through a form submission
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}
    
/* header.php and header_main.php open the db connection for us */
// Generate the report.

// Build the query based on the parameters: this will be a massive if statement.
$report=$_POST["id_report"];
switch ($report) {
    case "1": // All Sites
        $report_name = "List of all Active Sites";
        $filename = "data";
        $qshow = "SELECT concat('<a href=''edit_site.php?id=',Sites.id_site,'''>',name_site,'</a>') "
                    . "as 'Site Name', ";
        $qfile = "SELECT name_site as 'Site Name', ";
        $query = "  url_site as 'URL',
                    contact_site as 'Contact Info',
                    area_site as 'Area', 
                    street_site as 'Street Address',
                    city_site as 'City',
                    state_site as 'State',
                    zip_site as 'Zip Code',
                    verify_phone_site as 'Date phone verified',
                    verify_web_site as 'Date web verified',
                    verify_visit_site as 'Date visit verified',
                    facilities_site as 'Facilities',
                    capacity_site as 'Capacity',
                    rates_site as 'Rates'
                    FROM Sites 
                    WHERE active_site=1
                    ORDER by name_site;";
        break;
    case "2": // All Sites needing verification
        $report_name = "List of all Active Sites";
        $filename = "data";
        $qshow = "SELECT concat('<a href=''edit_site.php?id=',Sites.id_site,'''>',name_site,'</a>') "
                    . "as 'Site Name', ";
        $qfile = "SELECT name_site as 'Site Name', ";
        $query = "  url_site as 'URL',
                    contact_site as 'Contact Info',
                    area_site as 'Area', 
                    street_site as 'Street Address',
                    city_site as 'City',
                    state_site as 'State',
                    zip_site as 'Zip Code',
                    verify_phone_site as 'Date phone verified',
                    verify_web_site as 'Date web verified',
                    verify_visit_site as 'Date visit verified',
                    facilities_site as 'Facilities',
                    capacity_site as 'Capacity',
                    rates_site as 'Rates'
                    FROM Sites 
                    WHERE active_site=1 
                    AND 
                        (verify_phone_site is NULL 
                        OR verify_web_site is NULL 
                        OR verify_visit_site is NULL)
                    ORDER by name_site;";
        break;
        case "3": // All Sites
        $report_name = "List of all Inactive Sites";
        $filename = "data";
        $qshow = "SELECT concat('<a href=''edit_site.php?id=',Sites.id_site,'''>',name_site,'</a>') "
                    . "as 'Site Name', ";
        $qfile = "SELECT name_site as 'Site Name', ";
        $query = "  url_site as 'URL',
                    contact_site as 'Contact Info',
                    area_site as 'Area', 
                    street_site as 'Street Address',
                    city_site as 'City',
                    state_site as 'State',
                    zip_site as 'Zip Code',
                    verify_phone_site as 'Date phone verified',
                    verify_web_site as 'Date web verified',
                    verify_visit_site as 'Date visit verified',
                    facilities_site as 'Facilities',
                    capacity_site as 'Capacity',
                    rates_site as 'Rates'
                    FROM Sites 
                    WHERE active_site!= 1
                    ORDER by name_site;";
        break;

    default:
        echo '<p class="error"> No report selected.</p>';
        exit_with_footer();        
}

// Display data in $data
include 'report_showtable.php';

/* footer.php closes the db connection*/
