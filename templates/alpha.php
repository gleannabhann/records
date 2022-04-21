<p class="text-center">
<?php
// Build links to the list beginning with the appropriate initial, which is returned as $Initial
$query = "select count(*) as ct, substring(name_person,1,1) as Initial from Persons group by Initial";

  // or die ("Couldn't execute query");
echo form_subtitle("Click letter to list all people with that initial");
foreach ($cxn->query($query) as $row) {
//    extract($row);
    $init = $row['Initial'];
    $link = "<a href='/public/list.php?initial=$init'>$init</a>&nbsp";
    echo $link;
}
?>
</p>
