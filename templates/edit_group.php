<?php
// Allows user to change name, group, and kingdom
if (permissions("Herald")<3){
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}

if ((isset($_GET['id'])) && (is_numeric($_GET['id'])) && (isset($_SESSION['id'])) && (isset($_GET["name"]))) {
    // We got here through an edit link on search.php.
    $id_group = $_GET["id"];
    $search = $_GET["name"];
} elseif ((isset($_GET['id'])) && (is_numeric($_GET['id'])) && (isset($_SESSION['id']))) {
    // We got here through a non-search edit link.
    $id_group = $_GET["id"];
} elseif ((isset($_POST['id'])) && (is_numeric($_POST['id'])) && (isset($_SESSION['id']))) {
    // We got here from form submission
    // echo "Arrived as form submission";
    $id_group = $_POST["id"];
    $search = $_POST["name"];
} else  {
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}

$cxn = open_db_browse();

$query="SELECT id_group, name_group, id_kingdom "
        . "FROM Groups "
        . "WHERE Groups.id_group=:id_group;";
$data = [':id_group' => $id_group];
//echo "Query is :<br>$query<p>";

$sth_group = $cxn->prepare($query);
$sth_group->execute($data);
// fetch the first row. We're assuming there's only the one.
$group = $sth_group->fetch(PDO::FETCH_ASSOC);

$query = "SELECT id_kingdom, name_kingdom FROM Kingdoms;";
$sth_kingdoms = $cxn->prepare($query);
// not storing into variable because we'll fetch row-by-row later
$sth_kingdoms->execute();


echo "
<div class='row'>
  <div class='col-md-8 col-md-offset-2'>";

echo '<form action="edit_group.php" method="post">';
echo form_title("Editing Group Information");
if (isset($search)) {
  echo button_link("search.php?name=".$search, "Return to Search Page");
}
echo button_link("./list.php?group=$id_group", "List all Members of Group");
echo "<p>";
echo '<input type="hidden" name="id" value="'.$id_group.'">';
if (isset($search)) {
  echo '<input type="hidden" name="name" value="'.$search.'">';
}
echo "<table class='table table-condensed table-bordered'>";

$varname="name_group";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) 
{
    //echo "Using POST value for name_group<p>";
    $name_group=$_POST[$varname];
    $name_group = str_replace("'", "&#039;", $name_group);
} else {
  //echo "Using database value for name_group<p>";
    $name_group=$group[$varname];
}
if (DEBUG) {
    echo form_title("Group name is #".$name_group."#");
}
echo "<tr><td class='text-right'>Group Name</td>"
    . "<td><input type='text' name='name_group' value='$name_group'"
    . "</td></tr>";


$varname="id_kingdom";
if (isset($_POST[$varname]) && is_string($_POST[$varname])) {
    $id_kingdom=$_POST[$varname];
} else {
    $id_kingdom=$group[$varname];
}
echo "<tr><td class='text-right'>Kingdom of Award</td>";
echo '<td><select name="id_kingdom" ><option value="0"></option>';
while ($row= $sth_kingdoms->fetch(PDO::FETCH_ASSOC)) {
    echo '<option value="'.$row["id_kingdom"].'"';
    if ($row["id_kingdom"]==$id_kingdom) echo ' selected';
    echo '>'.$row["name_kingdom"].'</option>';
}
echo "</td></tr>";


echo "</table>";
echo '<input type="submit" value="Update Group Information">';
echo '</form>';

// Now let's update the database if and only if the form was posted
if (($_SERVER['REQUEST_METHOD'] == 'POST')  && (permissions("Herald")>=3)){
    // init the array for the prepared statement
    $data = [];
    // Need to replace any apostrophes in the new name
    $name_group = str_replace("'", "&#039;", $name_group);
    if (DEBUG) {
        echo "Updated name of group is: $name_group<p>";
    }
    $update="UPDATE Groups SET name_group=:name_group";
    $data[':name_group'] = $name_group;
    
    
    if ($id_kingdom!= $group["id_kingdom"]){
      $update=$update.", id_kingdom=:id_kingdom";
      $data[':id_kingdom'] = $id_kingdom;
    }
    $update=$update." WHERE id_group=:id_group";
    $data[':id_group'] = $id_group;
    if (DEBUG) {
       echo "Update query is:<br>$update<p>";
    } 
    $result=update_query($cxn, $update, $data);
    if ($result !== 1) {
      echo "Error updating record: \nPDO::errorInfo():";
      print_r($cxn->errorInfo());
    } else {
        echo "Updated $name_group.";
    }

}
echo "</div><!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs

$cxn = null; /* close the db connection */


