<div class="container">
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* the header connects to the database */


/* query: select a person's name for the header */
if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
    // We got here through a direct link on combat.php
    // echo "Arrived from person.php";
    $id_person = $_GET["id"];
} else {
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}

$ic=$_GET['id'];

$q_warr = "SELECT id_marshal, name_marshal, name_combat FROM Marshals, Combat where "
        . "Marshals.id_combat=Combat.id_combat "
        . "AND Marshals.id_combat=:ic";
$d_warr = [':ic' => $ic];
if (DEBUG) {echo "Warrants query: $q_warr<p>";}

try {
  $sth_warrs = $cxn->prepare($q_warr);
  $sth_warrs->execute($d_warr);
//$warrs = mysqli_query ($cxn, $q_warr) 
} catch (PDOException $e) {
  $error = "Couldn't execute query to find warrants to build report. ";
  if (DEBUG) {
    $message = $e->getMessage();
    $code = $e->getCode();
    $error = $error . "$message / $code";
  }
  bs_alert($error, 'warning');
}
  // If the combat id does not return any warrants at all 
if ($sth_warrs->rowCount() == 0) {
    bs_alert("This page has been accessed in error.", 'warning');
}

$qlink = "SELECT concat('<a href=''person.php?id=',PCC.id_person,'''>',name_person,'</a>') "
            . "as 'SCA Name', name_group as 'SCA Group',";
$qnolink = "SELECT name_person "
            . "as 'SCA Name', name_group as 'SCA Group',";

$q_head = "PCC.card_marshal as 'card number', "
        . "PCC.expire_marshal as 'expiry date' ";
$q_body = "FROM 
   (SELECT Persons.id_person, id_person_combat_card,  name_person, name_group, 
           card_marshal, expire_marshal  
    FROM Persons_CombatCards, Persons, Groups 
    WHERE Persons_CombatCards.id_person=Persons.id_person 
    AND Persons.id_group = Groups.id_group 
    AND Persons_CombatCards.expire_marshal >= curdate() 
    AND id_combat=:ic) AS PCC
           LEFT JOIN
    (SELECT COUNT(*) as num_count, id_person 
    FROM Persons_Marshals, Marshals
    WHERE Persons_Marshals.id_marshal=Marshals.id_marshal
    AND Marshals.id_combat=:id_combat
    GROUP BY id_person) AS PCount
    ON PCount.id_person = PCC.id_person ";
$data = [':ic' => $ic, ':id_combat' => $ic];
// Now we have to add the individual warrants
    while ($warr = $sth_warrs->fetch()) {
        extract($warr);
        $q_head = $q_head . ", if (PA$id_marshal.id_person IS NULL,'No', 'Yes') as '$name_marshal' ";    
        $q_body = $q_body . 
                "LEFT JOIN 
                   (SELECT id_person
                    FROM Persons_Marshals
                    WHERE Persons_Marshals.id_marshal=$id_marshal) AS PA$id_marshal
                    ON PA$id_marshal.id_person=PCC.id_person ";
    
    }
    $query = $qlink . $q_head . $q_body . "WHERE num_count is not NULL ORDER BY name_person";
    if (DEBUG) { echo "Warrants Query is<p> $query";}
    
    // This part borrowed from report_showtable, minus ability to download file
    try {
      $sth_result = $cxn->prepare($query);
      $sth_result->execute($data);
      //$data = mysqli_query ($cxn, $query) 
      } catch (PDOException $e) {
      $error = "Couldn't execute query to build table. ";
      if (DEBUG) {
        $message = $e->getMessage();
        $code = $d->getCode();
        $error = $error . "($message / $code)";
      }
      bs_alert($error, 'warning');
    }
    // Displays a table with sortable columns based on the data stored in $data.
    echo form_title("Active Marshals for $name_combat");
    foreach(range(0, $sth_result->columnCount() -1) as $i) {
      $fields[] = $sth_result->getColumnMeta($i);
    }
//    echo "<table class='table table-condensed table-bordered'>";
    echo '<table class="sortable table table-condensed table-bordered">';
    echo '<thead>';
    foreach ($fields as $field) {
            echo '<th>'.$field['name'].'</th>';
        }
        echo '</thead>';
    while ($row = $sth_result->fetch()) {
        echo '<tr>';
        foreach ($row as $field) {
            echo '<td>'.$field.'</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
    
?>
</div>
