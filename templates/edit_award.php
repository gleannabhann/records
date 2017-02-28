<?php
// Allows user to change name, group, and kingdom
if (permissions("Herald")<3){
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}

if ((isset($_GET['id'])) && (is_numeric($_GET['id'])) && (isset($_SESSION['id']))) {
    // We got here through the edit link on person.php
    // echo "Arrived from person.php";
    $id_award = $_GET["id"];
    $search = $_GET["name"];
} elseif ((isset($_POST['id'])) && (is_numeric($_POST['id'])) && (isset($_SESSION['id']))) {
    // We got here from form submission
    // echo "Arrived as form submission";
    $id_award = $_POST["id"];
    $search = $_POST["name"];
} else  {
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}

$cxn = open_db_browse();

$query="SELECT id_award, name_award, id_group, id_kingdom, id_rank "
        . "FROM Awards "
        . "WHERE Awards.id_award=$id_award;";
//echo "Query is :<br>$query<p>";

$result = mysqli_query ($cxn, $query) or die ("Couldn't execute query");
$award = mysqli_fetch_array($result);

$query = "SELECT id_group, "
        . "CONCAT(name_group,' (',name_kingdom,')') as name_group, "
        . "Groups.id_kingdom!=".HOST_KINGDOM_ID." as In_Kingdom "
        . "FROM Groups, Kingdoms "
        . "WHERE Groups.id_kingdom = Kingdoms.id_kingdom "
        . "AND id_group >= 0 "
        . "Order By In_Kingdom, name_group;";
//echo $query;
$groups = mysqli_query ($cxn, $query) or die ("Couldn't execute query");

$query = "SELECT id_kingdom, name_kingdom FROM Kingdoms;";
$kingdoms = mysqli_query ($cxn, $query) or die ("Couldn't execute query");

$query = "SELECT id_rank, name_rank FROM Ranks;";
$ranks = mysqli_query ($cxn, $query) or die ("Couldn't execute query");

echo "
<div class='row'>
  <div class='col-md-8 col-md-offset-2'>";

echo '<form action="edit_award.php" method="post">';
echo form_title("Editing Award Information");
echo button_link("search.php?name=".$search, "Return to Search Page");
echo button_link("./list.php?award=$id_award", "List all Recipients of Award");
echo "<p>";
echo '<input type="hidden" name="id" value="'.$id_award.'">';
echo '<input type="hidden" name="name" value="'.$search.'">';
echo "<table class='table table-condensed table-bordered'>";

$varname="name_award";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) 
{
    //echo "Using POST value for name_award<p>";
    $name_award=$_POST[$varname];
    $name_award = str_replace("'", "&#039;", $name_award);
} else {
    //echo "Using database value for name_award<p>";
    $name_award=$award[$varname];
}
if (DEBUG) {
    echo form_title("Award name is #".$name_award."#");
}
echo "<tr><td class='text-right'>Award Name</td>"
    . "<td><input type='text' name='name_award' value='$name_award'"
    . "</td></tr>";

$varname="id_group";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $id_group=$_POST[$varname];
} else {
    $id_group=$award[$varname];
}

echo "<tr><td class='text-right'>Group of Award (if any)</td>";
echo '<td><select name="id_group" ><option value="-1"></option>';
while ($row= mysqli_fetch_array($groups)) {
    echo '<option value="'.$row["id_group"].'"';
    if ($row["id_group"]==$id_group) echo ' selected';
    echo '>'.$row["name_group"].'</option>';
}
echo "</td></tr>";

$varname="id_kingdom";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $id_kingdom=$_POST[$varname];
} else {
    $id_kingdom=$award[$varname];
}
echo "<tr><td class='text-right'>Kingdom of Award</td>";
echo '<td><select name="id_kingdom" ><option value="0"></option>';
while ($row= mysqli_fetch_array($kingdoms)) {
    echo '<option value="'.$row["id_kingdom"].'"';
    if ($row["id_kingdom"]==$id_kingdom) echo ' selected';
    echo '>'.$row["name_kingdom"].'</option>';
}
echo "</td></tr>";


$varname="id_rank";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $id_rank=$_POST[$varname];
} else {
    $id_rank=$award[$varname];
}
echo "<tr><td class='text-right'>Rank of Award</td>";
echo '<td><select name="id_rank" ><option value="0"></option>';
while ($row= mysqli_fetch_array($ranks)) {
    echo '<option value="'.$row["id_rank"].'"';
    if ($row["id_rank"]==$id_rank) echo ' selected';
    echo '>'.$row["name_rank"].'</option>';
}
echo "</td></tr>";

echo "</table>";
echo '<input type="submit" value="Update Award Information">';
echo '</form>';

// Now let's update the database if and only if the for was posted
if (($_SERVER['REQUEST_METHOD'] == 'POST')  && (permissions("Herald")>=3)){
    // Need to replace any apostrophes in the new name
    $name_award = str_replace("'", "&#039;", $name_award);
    if (DEBUG) {
        echo "Updated name of award is: $name_award<p>";
    }
    $update="UPDATE Awards SET name_award='".$name_award."'";
    
    if ($id_group!= $award["id_group"]){
        $update=$update.", id_group=$id_group";
    }
    if ($id_kingdom!= $award["id_kingdom"]){
        $update=$update.", id_kingdom=$id_kingdom";
    }
    if ($id_rank!= $award["id_rank"]){
        $update=$update.", id_rank=$id_rank";
    }
    $update=$update." WHERE id_award=$id_award";
    if (DEBUG) {
       echo "Update query is:<br>$update<p>";
    } 
    $result=update_query($cxn, $update);
    if ($result !== 1) {
        echo "Error updating record: " . mysqli_error($cxn);
    } else {
        echo "Updated $name_award.";
    }

}
echo "</div><!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs

mysqli_close ($cxn); /* close the db connection */


