
<div class="container">
<?php
/* connect to the database */
$cxn = open_db_browse();

/* query: select a person's name for the header */
if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
    // We got here through a search link or directly link on person.php
    // echo "Arrived from person.php";
    $id_person = $_GET["id"];
} elseif ((isset($_POST['id'])) && (is_numeric($_POST['id']))) {
    // We got here from form submission after person reported correction
    // echo "Arrived as form submission";
    $id_person = $_POST['id'];
} else {
    echo '<p class="error"> This page has been accessed in error.</p>';
    exit_with_footer();
}

$query = "SELECT name_person, name_group, Groups.id_group "
        . "FROM Persons, Groups "
        . "WHERE Persons.id_person = $id_person "
        . "AND Persons.id_group=Groups.id_group";
if (DEBUG) {
    echo "Query to database is: $query<p>";
}
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
while ($row = mysqli_fetch_assoc($result))
  {extract($row);
  echo "<div class='page-header'>".form_title($name_person);
  echo form_subtitle("Member of ".live_link("list.php?group=$id_group", "$name_group"));
  include("../templates/warning.php"); // includes the warning text about paper precedence
  echo "</small>";
  if ((permissions("Herald")>= 3) or (permissions("Marshal")>=3)) {
      // TODO: Make this link more visible?
    echo "<br>".button_link(
        "./edit_person.php?id=$id_person",
        "Edit $name_person's record"
    );
  }
  echo "</div>";
};
echo "
<div class='row'>

  <div class='col-md-8 col-md-offset-2'>";

$query = "SELECT waiver_person, youth_person, birthdate_person
            FROM Persons
            WHERE id_person=$id_person";
if (DEBUG) {
    echo "Waiver query is:$query<p>";
}
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute waiver query");
$matches = $result->num_rows;
if ($matches > 0) {
    $row=mysqli_fetch_assoc($result);
    extract($row);
    echo form_subsubtitle("Combat waiver on file: $waiver_person");
    if ($waiver_person=='Parent') {
        echo form_subsubtitle("Parent or Legal Guardian's waiver on file:$youth_person");
    }
}
//if ($waiver_person != "No") { // No combat waiver?  No fight.
// Person may be authorized as a marshal without a combat waiver.
/* query: select a person's (non-expired) authorizations in the database */
    $query = "SELECT name_combat, name_auth, expire_authorize 
                FROM Persons_Authorizations, Authorizations, Combat, Persons_CombatCards
                WHERE Persons_CombatCards.id_person=$id_person 
                AND Persons_CombatCards.active_authorize='Yes' 
                AND Persons_Authorizations.id_person=$id_person
                AND curdate()<= expire_authorize
                AND Authorizations.id_combat=Combat.id_combat
                AND Persons_Authorizations.id_auth=Authorizations.id_auth
                AND Persons_CombatCards.id_combat = Combat.id_combat
                ORDER by name_combat, Authorizations.id_auth";
    if (DEBUG) {
        echo "Authorization query is:$query<p>";
    }
    $result = mysqli_query ($cxn, $query)
    or die ("Couldn't execute authorization query");
    $matches = $result->num_rows;
    if ($matches > 0) {
       $ocombat = "";
       echo form_subsubtitle("Authorizations on file:");
       while ($row = mysqli_fetch_assoc($result))
         {extract($row);
         if ($ocombat != $name_combat) {
            echo "<br><b>$name_combat (expires $expire_authorize)</b>: $name_auth";
         } else {
            echo ",&nbsp $name_auth";
         };
         $ocombat = $name_combat;
       }
       echo "<br>";
    }
    echo "<br>";

    /* query: select a person's marshal warrants in the database */
    $query = "SELECT name_combat, name_marshal, Persons_CombatCards.expire_marshal 
                FROM Persons_Marshals, Marshals, Combat, Persons_CombatCards
                WHERE Persons_CombatCards.id_person=$id_person 
                AND Persons_CombatCards.active_marshal='Yes' 
                AND Persons_Marshals.id_person=$id_person
                AND curdate()<= Persons_CombatCards.expire_marshal
                AND Marshals.id_combat=Combat.id_combat
                AND Persons_Marshals.id_marshal=Marshals.id_marshal
                AND Persons_CombatCards.id_combat = Combat.id_combat
                ORDER by name_combat, Marshals.id_marshal";
    //$query = "SELECT name_combat, name_marshal, expire_marshal
    //          FROM Persons_Marshals, Marshals, Combat
    //          WHERE Persons_Marshals.id_marshal = Marshals.id_marshal
    //          AND Marshals.id_combat = Combat.id_combat
    //          AND id_person = $id_person 
    //          AND curdate()<= expire_marshal 
    //          ORDER by name_combat, Marshals.id_marshal";
    if (DEBUG) {
        echo "Marshal Warrants query is:$query<p>";
    }

    $result = mysqli_query ($cxn, $query)
        or die ("Couldn't execute marshaling query");
    $matches = $result->num_rows;
    if ($matches > 0) {
       $ocombat = "";
       echo form_subsubtitle("Marshal's Warrants on file:");
       while ($row = mysqli_fetch_assoc($result))
         {extract($row);
         if ($ocombat != $name_combat) {
            echo "<br><b>$name_combat (expires $expire_marshal)</b>: $name_marshal";
         } else {
            echo ",&nbsp $name_marshal";
         };
         $ocombat = $name_combat;
       }
       echo "<br>";
    }
    echo "<br>";
//}
/* query: select a person's awards in the database  */
$query = "SELECT  Awards.id_award, name_award, date_award,name_kingdom, name_event, Events.id_event
          FROM Persons, Persons_Awards, Awards, Kingdoms, Events
          WHERE Persons.id_person = Persons_Awards.id_person
         AND Persons_Awards.id_award = Awards.id_award
         AND Awards.id_kingdom = Kingdoms.id_kingdom
         AND Persons_Awards.id_event = Events.id_event 
         AND Persons.id_person = $id_person order by date_award";
$result = mysqli_query ($cxn, $query) or die ("Couldn't execute awards query");
echo "<table class='table table-condensed table-bordered'>
<thead><td class='text-left'><strong>Award</strong></td>
<td class='text-left'><strong>Event</strong></td>
<td class='text-left'><strong>Date</strong></td></thead>";
while ($row = mysqli_fetch_assoc($result))
  {extract($row);
// echo "<tr><td class='text-left'>$name_award - $name_kingdom</td><td class='text-left'>$date_award</tr></td>";
  echo "<tr>";
  echo "<td class='text-left'><a href='list.php?award=$id_award'>$name_award</a></td>";
  if ($id_event > 0){
      echo "<td class='text-left'>"
      . "<a href='event.php?id=$id_event'>$name_event</a>"
      . "</td>";
  } else {
      echo "<td></td>";
  }
  echo "<td class='text-left'>$date_award</td>";
  echo "</tr>";
};
echo "</table>";
echo "</div><!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs
echo "<hr><p>Browse by Name:</p><p>";
include "alpha.php"; // includes the A-Z link list
mysqli_close ($cxn); /* close the db connection */
echo "<hr/>";

// If the submit button was pressed, handle the email.
if (isset($_POST["msgSubmit"])) {
    //TODO: Need to filter these fields carefully.
  $name = $_POST['name'];
  $email = $_POST['email'];
  $msgBody = wordwrap($_POST['msgBody']);
  $from = 'forms@records.gleannabhann.net';
  $to = 'webminister@gleannabhann.net';
//  $to = 'webminister@gleannabhann.net' . ', ';
//  $to .= 'obsidian@gleannabhann.net';
  $subject = $_POST['subject'];
  $body = "From: $name\n Email: $email\n Message:\n $msgBody";

  // check for name
  if (!$_POST['name']) {
    $errName = "Please enter your name";
  } else {$errName = false;}
  if (!$_POST['email']) {
    $errEmail = "Please enter your email address";
  } else {$errEmail = false;}
  if (!$_POST['msgBody']) {
    $errMessage = "Please enter information about the discrepancy or error";
  } else {$errMessage = false;}
  if (!$errName && !$errEmail && !$errMessage) {
     if (mail ($to, $subject, $body, $from)) {
       $emailresult = '<div class="alert alert-success">Thank you! We appreciate your feedback.</div>';
     } else {
       $emailresult ='<div class="alert alert-danger">I was unable to send your message. Please try again.</div>';
     }
   } else {
       echo "Error with setting up email.";
   }
}


?>
<!-- end of php -->
<!-- Problem report form hosted on forms.gleannabhann.net -->
<!-- element_3 is the email subject and is initialized with current record's name and id # -->
<script type="text/javascript">
var __machform_url = 'http://forms.gleannabhann.net/embed.php?id=10117&element_3=Records%20Correction%20for%20<?php echo "$name_person ($id_person)"?>';
var __machform_height = 751;
</script>
<div id="mf_placeholder"></div>
<script type="text/javascript" src="http://forms.gleannabhann.net/js/jquery.min.js"></script>
<script type="text/javascript" src="http://forms.gleannabhann.net/js/jquery.ba-postmessage.min.js"></script>
<script type="text/javascript" src="http://forms.gleannabhann.net/js/machform_loader.js"></script>
