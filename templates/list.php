<div class="container">
<?php
/* connect to the database */
$cxn = open_db_browse();
/*#######################################################################################*/
// This section wil list persons beginning with initial if initial is passed
echo "<!-- names beginning with initial results -->";
if (isset($_GET["initial"])) {
    $initial = $_GET["initial"];
    echo "<div class='page-header'><h1>Names beginning with $initial</h1><small>";
    include "warning.php"; // includes the warning text about paper precedence
    echo "</small></div>"; //Customize the page header
    echo "<div class='row justify-content-center'><div class='col-md-8 col-md-offset-2'>";
    include "alpha.php"; // includes the A-Z link list
    echo "<div class='list-group'><ul type='none'>"; // make the list pretty with formatting
    $initial = strtolower($initial);
    $query = "select id_person, name_person from Persons where substring(name_person,1,1) = :initial order by name_person";
    $data = ['initial' => $initial];
    $sth = $cxn->prepare($query);
    try
    { 
    $sth->execute($data);
    } catch (PDOException $e) {
      throw new DatabaseException($e->getMessage(), (int)$e->getCode());
    }
    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
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
echo "<!-- award results -->";
if (!isset($_GET["initial"]) && isset($_GET["award"])){
    $award = $_GET["award"];
    $query = "select name_award from Awards where id_award=:award";
    $data = ['award' => $award];
    $sth = $cxn->prepare($query);
    $sth->execute($data);
    $row = $sth->fetch(PDO::FETCH_ASSOC);
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
            . "and Persons_Awards.id_award=:award "
            . "order by name_person";
    $data = ['award' => $award];
    if (DEBUG) {
        echo "Group Query is: $query<p>";
    }    
    $sth = $cxn->prepare($query);
    $sth->execute($data);
    // open the table and table header
    echo '<table class="sortable table table-condensed table-bordered">';
    echo '<thead>';
    // given the total number of columns -1, request the column metadata for
    // that column's index and echo the column name as the next table header
    // cell
    foreach(range(0,$sth->columnCount() -1) as $index)
    {
      $col = $sth->getColumnMeta($index);
      echo '<th>'.$col['name'].'</th>';
    }
    // then close the table header
    echo '</thead>';
    echo '<tbody>';
    // build the table rows
    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        foreach ($row as $field) {
            echo '<td>'.$field.'</td>';
        }
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo "</div></div> <!-- ./col-md-8 -->"; //close out open divs
    echo "<p>";
    include "alpha.php"; // includes the A-Z link list
    include "warning.php"; // includes the warning text about paper precedence
}
/*#######################################################################################*/
// This section will list persons in a given group if group is passed
echo "<!-- group result -->";
if (!isset($_GET["initial"]) && !isset($_GET["award"]) && isset($_GET["group"])){
    $group = $_GET["group"];
    $query = "select name_group from Groups where id_group=:group";
    $data = ['group' => $group];
    $sth = $cxn->prepare($query);
    $sth->execute($data);
    $row = $sth->fetch(PDO::FETCH_ASSOC);
    $name_group = $row['name_group'];
    echo "<div class='page-header'><h1>Persons who belong to $name_group</h1></div>"; //Customize the page header
    echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
    if (permissions("Herald")>= 3) {
        $query = "SELECT concat('<a href=''edit_person.php?id=',Persons.id_person,'''>',name_person,'</a>') as 'SCA Name' ";
    } else {
        $query = "SELECT concat('<a href=''person.php?id=',Persons.id_person,'''>',name_person,'</a>') as 'SCA Name' ";        
    }
    $query = $query. " FROM Persons
              WHERE  Persons.id_group = :group ORDER BY 'SCA name'";
    $data = ['group' => $group];
    if (DEBUG) {
        echo "Group Query is: $query<p>";
    }
    $sth = $cxn->prepare($query);
    $sth->execute($data);
    // Note: Not sure why this query is executed twice into two variables, so
    // we're going to comment it out for now
    //$result = mysqli_query ($cxn, $query)
    //$data = mysqli_query ($cxn, $query) 
    //    or die ("Couldn't execute query to build report.");
    echo '<table class="sortable table table-condensed table-bordered">';
    echo '<thead>';
    foreach (range(0, $sth->getColumnCount() -1) as $index)
      {
      $col = $sth->getColumnMeta($index);
      echo '<th>'.$col['name'].'</th>';
      }
    echo '</thead>';
    echo '<tbody>';
    
    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        foreach ($row as $field) {
            echo '<td>'.$field.'</td>';
        }
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo "</div></div> <!-- ./col-md-8 -->"; //close out list and open divs
    echo "<hr>";
    include "alpha.php"; // includes the A-Z link list
    include "warning.php"; // includes the warning text about paper precedence
    
}
/*#######################################################################################*/
$cxn = null; /* close the db connection */
?>
</div>

