<?php

// This page changes armorial links and takes the following optional parameters
// ip -> id_person (always used)
// ia -> id_armorial (only used for *new* links)
// ipa -> id_person_armorial (only used for existing links)
// act -> make_badge, make_device, make_household, add_badge, add_device, add_household, delete (always used)


if (permissions("Ruby")< 3) { // This page is only accessible to the Ruby Herald
    //echo var_dump($_SESSION);
    bs_alert('<p class="error"> This page has been accessed in error by a non-Ruby Herald.</span>', 'warning');
    exit_with_footer();
}

if (isset($_GET["ip"])) { // Need person's id for return button
    $ip = $_GET["ip"];
} else {
    bs_alert('<span class="error"> This page has been accessed with incorrect parameters.</span>', 'warning');
    exit_with_footer();
}

if (isset($_GET["act"])) { // Need to know what to do
    $act = $_GET["act"];
} else {
    bs_alert("<span class='error'>This page has been accessed with incorrect parameters.</span>", 'warning');
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
        $query = "UPDATE Persons_Armorials SET type_armorial='device' WHERE id_person_armorial=:ipa";
        $data = [':ipa' => $ipa];
        $update = "Updated Record to change armorial to type device.";
        break;
    case "make_badge" :
        $query = "UPDATE Persons_Armorials SET type_armorial='badge' WHERE id_person_armorial=:ipa";
        $data = [':ipa' => $ipa];
        $update = "Updated Record to change armorial to type badge.";
        break;
    case "make_household" :
      $query = "UPDATE Persons_Armorials SET type_armorial='household' WHERE id_person_armorial=:ipa";
      $data = [':ipa' => $ipa];
        $update = "Updated Record to change armorial to type household.";
        break;
    case "delete" : // Extra careful when deleting a link
        if (isset($ipa) && is_numeric($ipa)) {
            $query = "DELETE FROM Persons_Armorials WHERE id_person_armorial=:ipa";
            $data = [':ipa' => $ipa];
            $update = "Updated Record to delete this link.";
        } else {
            bs_alert('<span class="error">Need a valid ipa</span>', 'warning');
            exit_with_footer();
        }
        break;
    case "add_device" :
        $query = "INSERT INTO Persons_Armorials (id_person, id_armorial, type_armorial) "
          . "VALUES (:ip, :ia, 'device')";
        $data = [':ip' => $ip, ':ia' => $ia];
        $update = "Added a new device link.";
        break;
    case "add_badge" :
        $query = "INSERT INTO Persons_Armorials (id_person, id_armorial, type_armorial) "
          . "VALUES (:ip, :ia, 'badge')";
        $data = [':ip' => $ip, ':ia' => $ia];
        $update = "Added a new badge link.";
        break;
    case "add_household" :
        $query = "INSERT INTO Persons_Armorials (id_person, id_armorial, type_armorial) "
          . "VALUES (:ip, :ia, 'household')";
        $data = [':ip' => $ip, ':ia' => $ia];
        $update = "Added a new household link.";
        break;
    default:
        bs_alert('<span class="error">There is no specified valid action</span>', 'warning');
        exit_with_footer();
}

/* header opens the db connection for us */

if (DEBUG) {
    echo "<p>Update query is: $query<br>";
    echo "Vars are " . json_encode($data) . "</p>";
}
$result = update_query($cxn, $query, $data);
if ($result !== 1) {
    bs_alert("<span class='error'>Error on attempt to: $update</span>", 'danger');
} else {
    bs_alert(form_subtitle($update), 'success');
}
echo button_link("edit_person.php?id=$ip", "Return to Edit Person page");
/* footer closes the db connection */
