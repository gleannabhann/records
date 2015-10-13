
<div class="container">
<?php
/* connect to the database */
//$cxn = mysqli_connect ("localhost", "oop", "ooppassword","oop")
//or die ("message");
$cxn = mysqli_connect (SERVER,USERNAME,PASSWORD,DATABASE)
or die ("message");

/* query: select a person's name for the header */
$id_person = $_GET["id"];
$query = "SELECT name_person from Persons WHERE Persons.id_person = $id_person";
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
while ($row = mysqli_fetch_assoc($result))
  {extract($row);
  echo "<div class='page-header'><h1>$name_person</h1><small>";
  include "warning.php"; // includes the warning text about paper precedence
  echo "</small></div>";
};
echo "
<div class='row'>

  <div class='col-md-8 col-md-offset-2'>";
echo "<table class='table table-condensed table-bordered'>
<thead><td class='text-left'><strong>Award</strong></td>
<td class='text-left'><strong>Date</strong></td></thead>";

/* query: select a person's authorizations in the database */
$id_person = $_GET["id"];
$query = "SELECT name_combat, name_auth, expire_auth 
          FROM Persons_Authorizations, Authorizations, Combat
          WHERE Persons_Authorizations.id_auth = Authorizations.id_auth
          AND Authorizations.id_combat = Combat.id_combat 
          AND id_person = $id_person
          ORDER by name_combat, Authorizations.id_auth";
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
$matches = $result->num_rows;
if ($matches > 0) {
   $ocombat = "";
   echo "<b> Authorizations on file:</b>";
   while ($row = mysqli_fetch_assoc($result))
     {extract($row);
     if ($ocombat != $name_combat) {
        echo "<br><b>$name_combat (expires $expire_auth)</b>: $name_auth";
     } else {
     	echo ",&nbsp $name_auth";
     };
     $ocombat = $name_combat;
   }
   echo "<br>";
}
echo "<br>";

/* query: select a person's marshal warrants in the database */
$id_person = $_GET["id"];
$query = "SELECT name_combat, name_marshal, expire_marshal 
          FROM Persons_Marshals, Marshals, Combat
          WHERE Persons_Marshals.id_marshal = Marshals.id_marshal
          AND Marshals.id_combat = Combat.id_combat 
          AND id_person = $id_person
          ORDER by name_combat, Marshals.id_marshal";
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
$matches = $result->num_rows;
if ($matches > 0) {
   $ocombat = "";
   echo "<b> Marshal's Warrants on file:</b>";
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

/* query: select a person's awards in the database  */
$id_person = $_GET["id"];
$query = "SELECT name_person, name_award, date_award,name_kingdom from Persons, Awards_Persons, Awards, Kingdoms
   WHERE Persons.id_person = Awards_Persons.id_person
         and Awards_Persons.id_award = Awards.id_award
         and Awards.id_kingdom = Kingdoms.id_kingdom
         and Persons.id_person = $id_person order by date_award";
$result = mysqli_query ($cxn, $query)
or die ("Couldn't execute query");
while ($row = mysqli_fetch_assoc($result))
  {extract($row);
// echo "<tr><td class='text-left'>$name_award - $name_kingdom</td><td class='text-left'>$date_award</tr></td>";
  echo "<tr><td class='text-left'>$name_award</td><td class='text-left'>$date_award</tr></td>";
};
echo "</table>";
echo "</div><!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs
echo "<hr><p>Browse by Name:</p><p>";
include "alpha.php"; // includes the A-Z link list
mysqli_close ($cxn); /* close the db connection */
echo "<hr/>";

if (isset($POST["msgSubmit"])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $msgBody = $_POST['msgBody'];
  $from = 'forms@oopgleannabhann.net';
  $to = 'obsidian@gleannabhann.net' . ', ';
  $to .= 'webminister@gleannabhann.net';
  $subject = $_POST['subject'];
  $body = "From: $name\n Email: $email\n Message:\n $msgBody";

  // check for name
  if (!$_POST['name']) {
    $errName = "Please enter your name";
  } else {$errName = "NULL";}
  if (!$_POST['email']) {
    $errEmail = "Please enter your email address";
  } else {$errEmail = "NULL";}
  if (!$_POST['message']) {
    $errMessage = "Please enter information about the discrepancy or error";
  } else {$errMessage = "NULL";}
if (!$errName && !$errEmail && !$errMessage) {
  if (mail ($to, $subject, $body, $from)) {
    $emailresult = '<div class="alert alert-success">Thank you! We appreciate your feedback.</div>';
  }
  else {
    $emailresult ='<div class="alert alert-danger">I was unable to send your message. Please try again.</div>';
  }
}
}


?>
<!-- end of php -->

<div class="row">
  <h2>Report a problem with this record:</h2>
  <form class="form-horizontal" role="form" method="post" action="person.php">
    <div class="form-group">
      <label for="name" class="col-sm-2 col-md-3 control-label">Name:</label>
      <div class="input-group col-sm-10 col-md-6">
        <span style="display: block; width: 100%"><input size="60" type="text" width="100%" class="form-control" id="name" name="name" placeholder="Your Name" value="<?php if (isset($_POST['msgSubmit'])) {echo htmlspecialchars($_POST['name']);} ?>"></span>
      </div>
    </div>
    <div class="form-group">
      <label for="email" class="col-sm-2 col-md-3 control-label">Email:</label>
      <div class="input-group col-sm-10 col-md-6">
  <input size="60" type="text" width="100%" class="form-control" id="email" name="email" placeholder="example@domain.com" value="<?php if (isset($_POST['msgSubmit'])) {echo htmlspecialchars($_POST['email']);} ?>">

      </div>
    </div>
    <div class="form-group">
      <label for="subject" class="col-sm-2 col-md-3 control-label">Subject:</label>
      <div class="input-group col-sm-10 col-md-6">
        <input size="60" type="text" class="form-control" id="subject" name="subject" value='<?php echo "Record Correction Request for $name_person (ID $id_person)"; ?>'>
      </div>
    </div>
    <div class="form-group">
      <label for="msgBody" class="col-sm-2 col-md-3 control-label">Details:</label>
      <div class="input-group col-sm-10 col-md-6">
          <textarea cols="62" class="form form-control" rows="4" name="msgBody" placeholder="Tell us what's incorrect about this record." id="msgBody" value="<?php if (isset($_POST['msgSubmit'])) {echo htmlspecialchars($_POST['msgBody']);} ?>">
        </textarea>

      </div>
    </div>

    <div class="form-group">
      <div class="input-group col-sm-10 col-sm-offset-2 col-md-6 col-md-offset-3">
        <input id="msgSubmit" name="msgSubmit" type="submit" value="Send" class="btn btn-primary">
      </div>
    </div>
    <div class="form-group">
      <div class="input-group col-sm-10 col-sm-offset-2 col-md-6 col-md-offset-3">
        <?php if(isset($emailresult)) {echo $emailresult;} ?>
      </div>
    </div>
  </form>


</div>
