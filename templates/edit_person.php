<?php

// Purpose: to display all data for person we're about to edit,
// including edit, delete, and add more links for awards
//
// Structure will consist of form (and form processing) for
// personal information, above a list of awards etc.  Each award
// will have edit/delete links, and there will be an add award at
//  the top of the list.
//
// Eventually the same structure will be added for authorization/warrants.
// ASSUMED: This page will only be reached by somebody who has the relevant
//          access privileges

echo "<div class='row'>";

// Checks for privs is conducted by code in config.php.

if ((!permissions("Herald")>=3) && (!permissions("Marshal")>=3)) {
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}

if ((isset($_GET['id'])) && (is_numeric($_GET['id'])) && (isset($_SESSION['id']))) {
    // We got here through the edit link on person.php
    // echo "Arrived from person.php";
    $id_person = $_GET["id"];
} elseif ((isset($_POST['id'])) && (is_numeric($_POST['id'])) && (isset($_SESSION['id']))) {
    // We got here from form submission
    // echo "Arrived as form submission";
    $id_person = $_POST['id'];
} else {
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}

/* header.php and header_main.php open the db connection */

//echo "Permissions for herald is ".permissions("Herald")."<br>";
//echo "<p>".var_dump($_SESSION)."<p>";

// this page can get really long if you have multiple perms, so we're going to
// build a navigation column on the left that will let you jump to each
// section, and make it sticky so you always have it.
//
//TODO when pulling up to Bootstrap 5, using an accordion might make more sense

// first we set up the vars
// Edit the personal information like name, mundane info, etc.
if ((permissions("Marshal")>=3) || (permissions("Herald")>=3)) {
    $personal = true;
}
// Edit devices and badges
if ((permissions("Ruby")>=3)) {
    $armorial = true;
}
// Edit authorization and warrant stuffs for person
if (permissions("Marshal")>= 3) {
    $auths = true;
}
// Edit awards for person
if ((permissions("Herald")>= 3) && (permissions("Obsidian")>=3)) {
    $awards = true;
}

// build the nav for smaller viewports - list-items are horizontal and small
// the 'fixed-top' class here won't work with BS3, but will with later versions
echo "<div class='visible-xs visible-sm col-xs-12 col-sm-12 fixed-top' role='nav'>";
echo "<p>Skip to:</p>";
echo "<ul class='list-group center-block' style='display: inline-block;'>";
if (isset($personal)) {
    echo "<li class='list-group-item' style='display: inline-block; padding: 5px'><a href='#personal'>Personal</a></li>";
}
if (isset($armorial)) {
    echo "<li class='list-group-item' style='display: inline-block; padding: 5px'><a href='#armorial'>Armorial</a></li>";
}
if (isset($auths)) {
    echo "<li class='list-group-item' style='display: inline-block; padding: 5px;'><a href='#auths'>Auths</a></li>";
    echo "<li class='list-group-item' style='display: inline-block; padding: 5px;'><a href='#marshal'>Marshal</a></li>";
}
if (isset($awards)) {
    echo "<li class='list-group-item' style='display: inline-block; padding: 5px'><a href='#awards'>Awards</a></li>";
}
echo "</ul>";
echo "</div>"; //end of nav column



// build the nav for larger viewports - list-items are vertical and normal size
// the 'fixed-top' class here won't work with BS3, but will with later versions
echo "<div class='hidden-xs hidden-sm col-md-3 col-lg-2 fixed-top' role='nav'>";
echo "<ul class='list-group'>";
if (isset($personal)) {
    echo "<li class='list-group-item'><a href='#personal'>Edit Personal Info</a></li>";
}
if (isset($armorial)) {
    echo "<li class='list-group-item'><a href='#armorial'>Edit Armorial Info</a></li>";
}
if (isset($auths)) {
    echo "<li class='list-group-item'><a href='#auths'>Edit Authorizations</a></li>";
    echo "<li class='list-group-item'><a href='#marshal'>Edit Marshal Data</a></li>";
}
if (isset($awards)) {
    echo "<li class='list-group-item'><a href='#awards'>Edit Awards</a></li>";
}
echo "</ul>";
echo "</div>"; //end of nav column

// build the main column
echo "<div class='col-sm-12 col-md-9 col-lg-8'>";
if (isset($personal)) {
    echo "<a name='personal'></a>";
    include 'edit_person_sub_personal_info.php';
    echo "<p class='text-center'><a href='#top'>Back to Top</a></p>";
}
if (isset($armorial)) {
    echo "<a name='armorial'></a>";
    include 'edit_person_sub_armorial.php';
    echo "<p class='text-center'><a href='#top'>Back to Top</a></p>";
}
if (isset($auths)) {
    echo "<a name='auths'></a>";
    include 'edit_person_sub_authorizations.php';
    echo "<p class='text-center'><a href='#top'>Back to Top</a></p>";
    echo "<a name='marshal'></a>";
    include 'edit_person_sub_marshals.php';
    echo "<p class='text-center'><a href='#top'>Back to Top</a></p>";
}
if (isset($awards)) {
    echo "<a name='awards'></a>";
    include 'edit_person_sub_awards.php';
    echo "<p class='text-center'><a href='#top'>Back to Top</a></p>";
}
echo "</div></div>";

/* footer.php closes the db connection */
