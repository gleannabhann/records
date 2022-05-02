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

$cxn = open_db_browse();
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    // TODO: We need to filter these variables much more carefully!
    $id_person = $_POST['id'];
    $id_award = $_POST["id_award"];
    $date_award = $_POST["date_award"];
    $date_exp = $_POST["date_exp"];
    $date_added = date("Y-m-d");
    $id_kingdom = $_POST["id_kingdom"];
    $id_event = $_POST["id_event"];
    
    $update = "INSERT INTO Persons_Awards VALUES "
            . "(NULL, $id_person, $id_award,"
            . "'$date_award','$date_exp','$date_added', $id_kingdom, $id_event )";
    //echo "Update Query is ".$update;
    $result=update_query($cxn, $update);
    if ($result !== 1) {echo "Error updating record: " . mysqli_error($cxn);}

}

$query = "SELECT name_person FROM Persons where id_person=".$id_person;
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute personal query");
if (mysqli_num_rows($result)==1) {
   $person=  mysqli_fetch_array($result);
} else {
    echo "Unable to find person in the database";
    exit_with_footer();
}

$query = "SELECT id_award, name_kingdom,"
        . "CONCAT(name_award,' (',name_kingdom,')') as Name_Award, "
        . "Awards.id_kingdom !=".HOST_KINGDOM_ID." as In_Kingdom "
        . "FROM Awards, Kingdoms "
        . "WHERE Awards.id_kingdom = Kingdoms.id_kingdom "
        . "ORDER BY In_Kingdom, name_kingdom, Name_Award;";
//echo $query;
$awards = mysqli_query ($cxn, $query) or die ("Couldn't execute awards query");

$query = "SELECT id_event, name_event, date_event_start, date_event_stop "
        . "FROM Events ORDER BY date_event_start DESC";
$events=mysqli_query ($cxn, $query) or die ("Couldn't execute list of events query");

$query="SELECT id_kingdom, name_kingdom from Kingdoms;";
$kingdoms = mysqli_query ($cxn, $query) or die ("Couldn't execute list of kingdoms query");
        
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
while ($row= mysqli_fetch_array($awards)) {
    echo '<option value="'.$row["id_award"].'"';
    if ($row["id_award"]==$id_award) echo ' selected';
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
while ($row= mysqli_fetch_array($events)) {
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
    $id_kingdom = HOST_KINGDOM_ID;
}
echo '<tr><td class="text-right">Awarded in:</td><td> <select name="id_kingdom" >';
while ($row= mysqli_fetch_array($kingdoms)) {
    echo '<option value="'.$row["id_kingdom"].'"';
    if ($row["id_kingdom"]==$id_kingdom) echo ' selected';
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
$cxn = null; /* close the db connection */
?>
