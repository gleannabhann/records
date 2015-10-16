<p>
<?php
// Build links to the list beginning with the appropriate initial, which is returned as $Initial
$query = "select count(*) as ct, substring(name_person,1,1) as Initial from Persons group by Initial";
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
while ($row = mysqli_fetch_assoc($result)) {
//    extract($row);
    $init = $row['Initial'];
    $link = "<a href='public/list.php?initial=$init'>$init</a>&nbsp";
    echo $link;
}
?>
</p>
