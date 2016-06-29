<?php  // open our php code
ini_set("display_errors", true);
error_reporting(E_ALL);
echo '<link href="/css/styles.css" rel="stylesheet"/>';
// NOTE: This test file assumes that if a group is selected, there is at least one marshal for that group

// In this section we construct the url for the API
$url="http://records.gleannabhann.net/api/list_marshals.php?id=";
echo $url;
if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
    // We got here through an api call 
    $ic = $_GET["id"];
    $url=$url."$ic";
    if ((isset($_GET['group'])) && (is_numeric($_GET['group']))) {
        $ig = $_GET["group"];  // If this is set then only marshals from one group are listed.
        $url=$url."&group=$ig";
    }
}else {
        $url= $url."1"; // default to using rapier as test case
}
    
// Now we retrieve the data
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
if(!$result){
    die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($c));
}
curl_close($ch);
$marshals = json_decode($result, TRUE);

$combat=$marshals["type_combat"];
$persons=$marshals["warrants"];

// Now we display the data in a table
echo "<h1> Now listing marshals for $combat</h1>";

//    echo "<table class='table table-condensed table-bordered'>";
echo '<table class="sortable table table-condensed table-bordered">';
//echo '<table>';
echo '<thead>';
    $fields = array_keys($persons[0][0]);
    for ($i = 1; $i < count($fields); $i++) {
    //foreach ($fields as $field) {
        echo '<th>'.$fields[$i].'</th>';
    }
    echo '</thead>';
for ($i=0; $i < count($persons); $i++) {
    $row = $persons[$i][0];
    echo '<tr>';
    echo "<td><a href='http://records.gleannabhann.net/public/person.php?id=".$row["id_person"]."'>"
       . $row["SCA Name"]."</a></td>";
    for ($j = 2; $j < count($fields); $j++){
        $field=$fields[$j];
        echo "<td>$row[$field]</td>";
    }
    echo '</tr>';
}
echo '</table>';
?>
