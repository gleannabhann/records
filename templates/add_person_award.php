<?php
/*
 * Adds another award for the person whose id is passed in.
 */
if (permissions("Herald")< 3) {
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}

if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
    // We got here through the edit link on person.php
    // echo "Arrived from person.php";
    $id_person = $_GET["id"];
} elseif ((isset($_POST['id'])) && (is_numeric($_POST['id']))) {
    // We got here from form submission
    // echo "Arrived as form submission";
    $id_person = $_POST['id'];
} else {
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // TODO: We need to filter these variables much more carefully!
    $id_person = $_POST['id'];
    $id_award = $_POST["id_award"];
    $date_award = $_POST["date_award"];
    $date_exp = $_POST["date_exp"];
    $date_added = date("Y-m-d");
    $id_kingdom = $_POST["id_kingdom"];
    $id_event = $_POST["id_event"];

    // preload the data array
    $data = [
      ':id_person' => $id_person,
      ':id_award' => $id_award,
      ':date_award' => $date_award,
      ':date_added' => $date_added,
      ':id_kingdom' => $id_kingdom,
      ':id_event' => $id_kingdom,
    ];
    // if no expiration date was set, we need to form the query differently
    if ($date_exp == '') {
        // no exp date
        $update = "INSERT INTO Persons_Awards VALUES "
              . "(NULL, :id_person, :id_award,"
              . ":date_award, NULL, :date_added, :id_kingdom, :id_event)";
    } else {
        $update = "INSERT INTO Persons_Awards VALUES "
              . "(NULL, :id_person, :id_award,"
              . ":date_award, :date_exp, :date_added, :id_kingdom, :id_event)";
        $data[':date_exp'] = $date_exp;
    }
    try {
        $result=update_query($cxn, $update, $data);
        // update was successful, let's tell the user
        $f_msg = "Success! Award added!";
        echo "<div class='row'><div class='col-sm-12 col-md-8 col-md-offset-2'>";
        bs_alert($f_msg, 'success');
        echo "</div></div>";
    } catch (PDOException $e) {
        $f_msg = "Couldn't add the Award.";
        echo "<div class='row'><div class='col-sm-12 col-md-8 col-md-offset-2'>";
        bs_alert($f_msg, 'danger');
        echo "</div></div>";
        if (DEBUG) {
            $vars = ['query' => $update, 'data' => $data];
            log_debug($f_msg, $vars, $e);
        }
    }
}

// TODO: rewrite q_kingdom to lookup the host kingdom id inline

$q_person = "SELECT name_person FROM Persons where id_person=:id_person";
$data = [':id_person' => $id_person];
$q_k_id = "SELECT host_kingdom_id FROM Appdata where app_id=1";
try {
    $sth_person = $cxn->prepare($q_person);
    $sth_person->execute($data);
    $sth_k_id = $cxn->query($q_k_id);
    $k_id = $sth_k_id->fetch();
    $person = $sth_person->fetch();
} catch (PDOException $e) {
    $f_msg = "Couldn't fetch data to build the form";
    echo "<div class='row'><div class='col-sm-12 col-md-8 col-md-offset-2'>";
    bs_alert($f_msg, 'danger');
    echo "</div></div>";
    if (DEBUG) {
        $vars = ['q_person' => $q_person, 'data' => $data, 'q_k_id' => $q_k_id];
        log_debug($f_msg, $vars, $e);
    }
    // no point continuing
    exit_with_footer;
}


$q_awards = "SELECT id_award, name_kingdom,"
        . "CONCAT(name_award,' (',name_kingdom,')') as Name_Award, "
        . "Awards.id_kingdom != :host_k_id as In_Kingdom "
        . "FROM Awards, Kingdoms "
        . "WHERE Awards.id_kingdom = Kingdoms.id_kingdom "
        . "ORDER BY In_Kingdom, name_kingdom, Name_Award;";
$d_awards = [':host_k_id' => $k_id['host_kingdom_id']];

$q_events = "SELECT id_event, name_event, date_event_start, date_event_stop "
        . "FROM Events ORDER BY date_event_start DESC";
$q_kingdoms ="SELECT id_kingdom, name_kingdom from Kingdoms;";
try {
    $sth_awards = $cxn->prepare($q_awards);
    $sth_awards->execute($d_awards);
    $sth_events = $cxn->query($q_events);
    $sth_kingdoms = $cxn->query($q_kingdoms);
} catch (PDOException $e) {
    $f_msg = "Couldn't fetch data to build the form";
    echo "<div class='row'><div class='col-sm-12 col-md-8 col-md-offset-2'>";
    bs_alert($f_msg, 'danger');
    echo "</div></div>";
    if (DEBUG) {
        $vars = [
      'q_awards' => $q_awards,
      'd_awards' => $d_awards,
      'q_events' => $q_events,
      'q_kingdoms' => $q_kingdoms,
    ];
        log_debug($f_msg, $vars, $e);
    }
    // no point continuing
    exit_with_footer;
}

echo "<div class='row'>
  <div class='col-md-8 col-md-offset-2'>";
echo '<form action="add_person_award.php" method="post">';
echo form_title('Adding a New Award for '.
        '<a href="edit_person.php?id='.$id_person.'">'
        . $person["name_person"].'</a>');
echo '<input type="hidden" name="id" value="'.$id_person.'">';
echo "<table class='table table-condensed table-bordered'>";
// Date the award was handed out
if (isset($_POST["date_award"]) && is_string($_POST["date_award"])) {
    $date_award = $_POST["date_award"];
} else {
    $date_award = date("Y-m-d"); // defaults to today's date
}
echo '<tr><td class="text-right">Date awarded:</td><td> '
    .'<input type="date" class="date" name="date_award" value="'
    . $date_award . '"> (format if no datepicker: yyyy-mm-dd)</td></tr>';

// Date the award expires for awards like champion
if (isset($_POST["date_exp"]) && is_string($_POST["date_exp"])) {
    $date_exp = $_POST["date_exp"];
} else {
    $date_exp = ''; // defaults to null
}
echo '<tr><td class="text-right">Date expires:</td><td> '
    . '<input type="date" class="date" name="date_exp" value="'
    . $date_exp . '"> (Only for awards with fixed term)<br>'
    . '(format if no datepicker: yyyy-mm-dd)</td></tr>';

// Actual award selected from list
if (isset($_POST["id_award"]) && is_numeric($_POST["id_award"])) {
    $id_award = $_POST["id_award"];
} else {
    $id_award = -1;
}
echo '<tr><td class="text-right">Award:</td><td> <select name="id_award" >';
while ($row= $sth_awards->fetch()) {
    echo '<option value="'.$row["id_award"].'"';
    if ($row["id_award"]==$id_award) {
        echo ' selected';
    }
    echo '>'.$row["Name_Award"].'</option>';
}
echo '</select></td></tr>';

// Event at which award was presented
if (isset($_POST["id_event"]) && is_numeric($_POST["id_event"])) {
    $id_event = $_POST["id_event"];
} else {
    $id_event = -1;
}
echo '<tr><td class="text-right">Event:</td><td> <select name="id_event" >';
while ($row= $sth_events->fetch()) {
    echo '<option value="'.$row["id_event"].'"';
    if ($row["id_event"]==$id_event) {
        echo ' selected';
    }
    echo ">".$row["name_event"]." (".$row["date_event_start"].
            " - ".$row["date_event_stop"].")</option>";
}
echo "</select></td></tr>";

// Kingdom in which award was given out, defaults to home kingdom
if (isset($_POST["id_kingdom"]) && is_numeric($_POST["id_kingdom"])) {
    $id_kingdom = $_POST["id_kingdom"];
} else {
    $id_kingdom = $k_id['host_kingdom_id'];
}
echo '<tr><td class="text-right">Awarded in:</td><td> <select name="id_kingdom" >';
while ($row= $sth_kingdoms->fetch()) {
    echo '<option value="'.$row["id_kingdom"].'"';
    if ($row["id_kingdom"]==$id_kingdom) {
        echo ' selected';
    }
    echo '>'.$row["name_kingdom"].'</option>';
}
echo '</select></td></tr>';

echo "</table>";
if (!isset($_POST["id"])) { // Allow submit button only if this is new.
    echo '<input type="submit" value="Add this award" class="btn btn-primary">';
} else {
    echo "<a href='./add_person_award.php?id=$id_person'>Add another new award</a>";
}


echo "<p>";
