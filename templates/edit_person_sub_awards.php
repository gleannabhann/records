<?php
// Part of the edit_person.php file
echo form_title("Editing awards");
echo button_link("./add_person_award.php?id=".$id_person, "Add a new Award for ".$sca_name);
echo "<table class='table table-condensed table-bordered'>\n
<thead><td class='text-left'><strong>Award</strong></td>\n
<td class='text-left'><strong>Date</strong></td>
<td class='text-left'><strong>Event</strong></td>
<td>Edit</td><td>Delete</td></thead>\n";

// Display person's awards with edit & delete link for each award
 $query = "SELECT  id_person_award, name_award, date_award,name_kingdom, 
     Awards.id_award, name_event, Events.id_event 
     FROM Persons, Persons_Awards, Awards, Kingdoms, Events
     WHERE Persons.id_person = Persons_Awards.id_person
         and Persons_Awards.id_award = Awards.id_award
         and Awards.id_kingdom = Kingdoms.id_kingdom
         and Persons_Awards.id_event = Events.id_event 
         and Persons.id_person = :id_person order by date_award";
$data = ['id_person' => $id_person];
if (DEBUG) { echo "Query to list awards is: ".$query."<br>";}
$sth = $cxn->prepare($query);
$sth->execute($data);
while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
  extract($row);
// echo "<tr><td class='text-left'>$name_award - $name_kingdom</td><td class='text-left'>$date_award</tr></td>";
  echo "<tr><td class='text-left'><a href='list.php?award=$id_award'>$name_award</a></td>";
  echo "<td class='text-left'>$date_award</td>\n";
  if ($id_event>0){
      echo "<td class='text-left'>$name_event</td>";
  } else {
      echo "<td></td>";
  }
  echo "<td>".button_link("./edit_person_award.php?idpa=".$id_person_award."&id=".$id_person, "Edit Date/Event")."</td>\n";
  echo "<td>".button_link(
      "./delete_person_award.php?id=".$id_person."&idpa=".$id_person_award,
      "Delete Award"
  )."</td>\n";
  echo "</tr>";
};
echo "</table>";

?>
