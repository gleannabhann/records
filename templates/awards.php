<?php
/* connect to the database */
$cxn = open_db_browse();

echo "<div class='row'><div class='col-md-6 col-md-offset-3'>";


///////////////////////////////////////////////////////////////////////////////
// Main portion of the page
///////////////////////////////////////////////////////////////////////////////
echo "<div class='center-block'>";
include "alpha.php";
echo "</div>";
echo "<div class='center-block'>";
echo form_subtitle("Search on a Partial Name");
    echo '<form role="search" action="search.php" method="get">';
    echo  '<input type="text" class="form-control" '
    . 'placeholder="Search for Name or Award" name="name">';
    echo '<button type="submit" class="btn btn-default">Submit</button>';

echo "</div>";
echo "</div><!-- ./col-md-6 -->";

///////////////////////////////////////////////////////////////////////////////
// News column on right
///////////////////////////////////////////////////////////////////////////////
echo '<div class="col-md-3 well">';
echo form_subtitle("Most recent awards");
$query = "SELECT Persons.id_person, name_person, name_award, date_award "
        . "FROM Persons, Persons_Awards, Awards "
        . "WHERE Persons.id_person=Persons_Awards.id_person "
        . "AND Awards.id_award = Persons_Awards.id_award "
        . "ORDER BY date_award DESC "
        . "LIMIT 20";
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute query");
while ($row = mysqli_fetch_assoc($result)) {
    extract($row);
    echo "<li><a href='edit.php?id=$id_person'>"
            . "$name_person</a> received $name_award on $date_award"
            . "</li>";
}

include "warning.php"; // includes the warning text about paper precedence

echo "<div> <!-- ./col-md-3 --></div> <!-- ./row -->";


mysqli_close ($cxn); /* close the db connection */
?>
