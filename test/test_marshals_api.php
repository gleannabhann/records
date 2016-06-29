<?php  // open our php code
ini_set("display_errors", true);
error_reporting(E_ALL);
echo '<link href="/css/styles.css" rel="stylesheet"/>';
        
$ch = curl_init("131.95.204.15/api/list_marshals.php?id=1");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
if(!$result){
    die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($c));
}
curl_close($ch);
$marshals = json_decode($result, TRUE);

//print_r($marshals);
$combat=$marshals["type_combat"];
$persons=$marshals["warrants"];

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
