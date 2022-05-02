<?php
// This page changes armorial links and takes the following optional parameters
// ip -> id_person (always used)
// ia -> id_armorial (only used for *new* links)
// ipa -> id_person_armorial (only used for existing links)
// act -> make_badge, make_device, make_household, add_badge, add_device, add_household, delete (always used)


if (permissions("Ruby")< 3) { // This page is only accessible to the Ruby Herald
    //echo var_dump($_SESSION);
    echo '<p class="error"> This page has been accessed in error by a non-Ruby Herald.</p>';
    exit_with_footer();
}

if (isset($_GET["ip"])) { // Need person's id for return button
    $ip = $_GET["ip"];
} else {
    echo '<p class="error"> This page has been accessed with incorrect parameters.</p>';
    exit_with_footer();
}

if (isset($_GET["act"])){ // Need to know what to do
    $act = $_GET["act"];
} else {
    echo '<p class="error"> This page has been accessed with incorrect parameters.</p>';
    exit_with_footer();
}

if (isset($_GET["ipa"])) {
    $ipa = $_GET["ipa"];
}

if (isset($_GET["ia"])) {
    $ia = $_GET["ia"];
}

switch ($act) { // This will set the update/insert query, or exit the page if no good action is passed
    case "make_device" : // Note: currently we are not checking that person has at most one device
        $query = "UPDATE Persons_Armorials SET type_armorial='device' WHERE id_person_armorial=$ipa";
        $update = "Updated Record to change armorial to type device.";
        break;
    case "make_badge" :
        $query = "UPDATE Persons_Armorials SET type_armorial='badge' WHERE id_person_armorial=$ipa";
        $update = "Updated Record to change armorial to type badge.";
        break;
    case "make_household" :
        $query = "UPDATE Persons_Armorials SET type_armorial='household' WHERE id_person_armorial=$ipa";
        $update = "Updated Record to change armorial to type household.";
        break;
    case "delete" : // Extra careful when deleting a link
        if (isset($ipa) && is_numeric($ipa)) {
            $query = "DELETE FROM Persons_Armorials WHERE id_person_armorial=$ipa";
            $update = "Updated Record to delete this link.";
        } else {
            echo '<p class="error">Need a valid ipa</p>';
            exit_with_footer();          }
        break;
    case "add_device" :
        $query = "INSERT INTO Persons_Armorials (id_person, id_armorial, type_armorial) "
            . "VALUES ($ip, $ia, 'device')";
        $update = "Added a new device link.";
        break;
    case "add_badge" :
        $query = "INSERT INTO Persons_Armorials (id_person, id_armorial, type_armorial) "
            . "VALUES ($ip, $ia, 'badge')";
        $update = "Added a new badge link.";
        break;
    case "add_household" :
        $query = "INSERT INTO Persons_Armorials (id_person, id_armorial, type_armorial) "
            . "VALUES ($ip, $ia, 'household')";
        $update = "Added a new household link.";
        break;
    default:
        echo '<p class="error">There is no specified valid action</p>';
        exit_with_footer();
}

$cxn = open_db_browse();
if (DEBUG) {
    echo "Update query is: $query";
}
$result = update_query($cxn, $query);
if ($result !== 1) {
    echo "Error on attempt to: $update";
} else {
    echo form_subtitle($update);
}
echo button_link("edit_person.php?id=$ip", "Return to Edit Person page");
$cxn = null; /* close the db connection */
?>
