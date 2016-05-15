<div class="container">
<?php
/* connect to the database */
$cxn = open_db_browse();
/*#######################################################################################*/
// This section wil list persons beginning with initial if initial is passed
if (isset($_GET["initial"])) {
    $Initial = $_GET["initial"];
    echo "<div class='page-header'><h1>Names beginning with $Initial</h1><small>";
    include "warning.php"; // includes the warning text about paper precedence
    echo "</small></div>"; //Customize the page header
    echo "<div class='row'><div class='col-md-8 offset-md-2'>";
    include "alpha.php"; // includes the A-Z link list
    echo "<div class='list-group'><ul type='none'>"; // make the list pretty with formatting
    $query = "select id_person, name_person from Persons "
            . "where upper(substring(name_person,1,1)) ='$Initial'"
            . "order by name_person";
    $result = mysqli_query ($cxn, $query)
    or die ("Couldn't execute query");
    while ($row = mysqli_fetch_assoc($result)) {
    //    extract($row);
        $Name = $row['name_person'];
        $ID = $row['id_person'];
        $link = "<li class='list-group-item text-left'><a href='./person.php?id=$ID'>$Name</a></li>";
    //    $link = "<li> $Name </li>";
        echo "$link";
    }
    echo "</ul></div> <!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs
}
/*#######################################################################################*/
// This section will list persons with a given award if award is passed
if (!isset($_GET["initial"]) && isset($_GET["award"])){
    $award = $_GET["award"];
    $query = "select name_award from Awards where id_award=$award";
    $result = mysqli_query ($cxn, $query)
    or die ("Couldn't execute query");
    $row = mysqli_fetch_assoc($result);
    $name_award = $row['name_award'];
    echo "<div class='page-header'><h1>Persons who hold the award $name_award</h1></div>"; //Customize the page header
    echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
    //echo "<div class='list-group'><ul type='none'>"; // make the list pretty with formatting
    if (permissions("Herald")>= 3) {
        $query = "SELECT concat('<a href=''edit_person.php?id=',Persons.id_person,'''>',name_person,'</a>') as 'SCA Name', ";
    } else {
        $query = "SELECT concat('<a href=''person.php?id=',Persons.id_person,'''>',name_person,'</a>') as 'SCA Name', ";        
    }
    $query = $query . " date_award as 'Date Awarded'"
            . "from Persons, Persons_Awards "
            . "where Persons.id_person = Persons_Awards.id_person "
            . "and Persons_Awards.id_award=$award "
            . "order by name_person";
    if (DEBUG) {
        echo "Group Query is: $query<p>";
    }    
    $result = mysqli_query ($cxn, $query)
    or die ("Couldn't execute query");
    $data = mysqli_query ($cxn, $query) 
        or die ("Couldn't execute query to build report.");
    $fields = mysqli_fetch_fields($data);
     echo '<table class="sortable table table-condensed table-bordered">';
    echo '<thead>';
        foreach ($fields as $field) {
            echo '<th>'.$field->name.'</th>';
        }
        echo '</thead>';
    while ($row = mysqli_fetch_assoc($data)) {
        echo '<tr>';
        foreach ($row as $field) {
            echo '<td>'.$field.'</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
    echo "</div></div> <!-- ./col-md-8 -->"; //close out open divs
    echo "<p>";
    include "alpha.php"; // includes the A-Z link list
    include "warning.php"; // includes the warning text about paper precedence
}
/*#######################################################################################*/
// This section will list persons in a given group if group is passed
if (!isset($_GET["initial"]) && !isset($_GET["award"]) && isset($_GET["group"])){
    $group = $_GET["group"];
    $query = "select name_group from Groups where id_group=$group";
    $result = mysqli_query ($cxn, $query)
    or die ("Couldn't execute query");
    $row = mysqli_fetch_assoc($result);
    $name_group = $row['name_group'];
    echo "<div class='page-header'><h1>Persons who belong to $name_group</h1></div>"; //Customize the page header
    echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
    if (permissions("Herald")>= 3) {
        $query = "SELECT concat('<a href=''edit_person.php?id=',Persons.id_person,'''>',name_person,'</a>') as 'SCA Name' ";
    } else {
        $query = "SELECT concat('<a href=''person.php?id=',Persons.id_person,'''>',name_person,'</a>') as 'SCA Name' ";        
    }
    $query = $query. " FROM Persons
              WHERE  Persons.id_group = $group ORDER BY 'SCA name'";
    
    if (DEBUG) {
        echo "Group Query is: $query<p>";
    }
    $result = mysqli_query ($cxn, $query)
    or die ("Couldn't execute query");
    $data = mysqli_query ($cxn, $query) 
        or die ("Couldn't execute query to build report.");
    $fields = mysqli_fetch_fields($data);
     echo '<table class="sortable table table-condensed table-bordered">';
    echo '<thead>';
        foreach ($fields as $field) {
            echo '<th>'.$field->name.'</th>';
        }
        echo '</thead>';
    while ($row = mysqli_fetch_assoc($data)) {
        echo '<tr>';
        foreach ($row as $field) {
            echo '<td>'.$field.'</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
    echo "</div></div> <!-- ./col-md-8 -->"; //close out list and open divs
    echo "<p>";
    include "alpha.php"; // includes the A-Z link list
    include "warning.php"; // includes the warning text about paper precedence
    
}
/*#######################################################################################*/
mysqli_close ($cxn); /* close the db connection */
?>
</div>

