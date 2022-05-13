<div class="container">
<?php

if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) {
    // We got here through a search link or directly link on list_events.php
    $id_event = $_GET["id"];
} else {
    $error = 'This page has been accessed in error; need to select an event.</p>';
    bs_alert($error, 'warning');
    exit_with_footer();
}

/* header.php or header_main.php has connected to the database for us */

$query = "SELECT name_event, date_event_start, date_event_stop, name_group, id_site, Events.id_group "
        . "FROM Events, Groups "
        . "WHERE id_event=:id_event "
        . "AND Events.id_group= Groups.id_group;";
$data = [':id_event' => $id_event];
if (DEBUG) {
    echo "Event Information Query is:<p>$query<p>";
}
try {
    $sth = $cxn->prepare($query);
    $sth->execute($data);
    $event_info = $sth->fetch();
} catch (PDOException $e) {
    $error = "Couldn't execute get the event info.";
    if (DEBUG) {
        $message = $e->getMessage();
        $code = (int)$e->getCode();
        $error = $error . " $message / $code ";
    }
    bs_alert($error, 'danger');
    exit_with_footer();
}
extract($event_info);
if ($id_site<0) {
    if (DEBUG) {
        echo "Event record doesn't include Site ID<p>";
    }
    $name_site="Unknown";
} else {
    $query="SELECT name_site FROM Sites where id_site=:id_site";
    $data = [':id_site' => $id_site];
    try {
        $sth = $cxn->prepare($query);
        $sth->execute($data);
        $name_site = $sth->fetch();
        $name_site = $name_site['name_site'];
    } catch (PDOException $e) {
        $error = "Couldn't find information about the event site.";
        if (DEBUG) {
            $message = $e->getMessage();
            $code = (int)$e->getCode();
            $error = $error . " $message / $code ";
        }
        bs_alert($error, 'danger');
        exit_with_footer();
    }
}

/* Display the known information of the event */

echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
echo form_title("$name_event");
echo form_subtitle("Hosted by <a href='list.php?group=$id_group'>$name_group</a> from $date_event_start to $date_event_stop");
if (!is_null($id_site)) {
    echo form_subtitle("Held at <a href='site.php?id=$id_site'>$name_site</a>");
}
if (permissions("Herald")>=3) {
    echo button_link("edit_event.php?id=$id_event", "Edit Event Information");
}
/* Display Known Award Recipients at this event */
$query = "SELECT Persons.id_person, Awards.id_award, name_person, name_award "
        . "FROM Persons, Awards, Persons_Awards "
        . "WHERE Persons.id_person = Persons_Awards.id_person "
        . "AND Awards.id_award = Persons_Awards.id_award "
        . "AND Persons_Awards.id_event = :id_event;";
$data = [':id_event' => $id_event];
if (DEBUG) {
    echo "<p>The Recipients Query is:<p>$query</p><p>Vars:<br>";
    print_r($data);
    echo "</p>";
}
try {
    $sth = $cxn->prepare($query);
    $sth->execute($data);
    $matches = $sth->rowCount();
} catch (PDOException $e) {
    $error = "Couldn't fetch the award recipients.";
    if (DEBUG) {
        $message = $e->getMessage();
        $code = (int)$e->getCode();
        $error = $error . " $message / $code ";
    }
    bs_alert($error, 'danger');
    exit_with_footer();
}
  echo form_subtitle("Award Recipients At $name_event");

if ($matches > 0) {
    echo "<table class='table table-condensed table-bordered'>
    <thead>
    <td ><strong>Recipient</strong></td>
    <td class='text-left'><strong>Award</strong></td>
    </thead>";
    while ($row = $sth->fetch()) {
        extract($row);
        echo "<tr>";
        echo "<td ><a href='person.php?id=$id_person'>$name_person</a></td>";
        echo "<td class='text-left'><a href='list.php?award=$id_award'>$name_award</a></td>";
        echo "</tr>";
    }
    echo "</table><p>";
} else {
    echo "<p>Currently no recipients known for this event.<p>";
}
echo "</div><!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs


if (permissions("Herald")>= 3) {
    /* Now let's list potential recipients for the herald's eyes only */
    echo "<div class='row'><div class='col-md-8 col-md-offset-2'>";
    echo form_subtitle("People Who May Have Received This Award At This Event");
    echo form_subtitle("(due to the date of the Award)");
    $query = "SELECT Persons.id_person, name_person, name_award, Awards.id_award "
            . "FROM Persons, Awards, Persons_Awards "
            . "WHERE id_event=-1 "
            . "AND date_award >= ':date_event_start' "
            . "AND date_award <= ':date_event_stop' "
            . "AND Persons.id_person = Persons_Awards.id_person "
            . "AND Awards.id_award = Persons_Awards.id_award";
    $data = [':date_event_start' => $date_event_start, ':date_event_stop' => $date_event_stop];
    if (DEBUG) {
        echo "Potential Recipients query is:<p>$query</p><p>vars are:<br>";
        print_r($data);
        echo "</p>";
    }
    try {
        $sth = $cxn->prepare($query);
        $sth->execute($data);
    } catch (PDOException $e) {
        $error = "Couldn't fetch potential recipients data. Please notify the administrator.";
        if (DEBUG) {
            $message = $e->getMessage();
            $code = (int)$e->getCode();
            $error = $error . " $message / $code ";
        }
        bs_alert($error, 'danger');
        exit_with_footer();
    }


    echo "<table class='table table-condensed table-bordered'>
<thead>
<td ><strong>Recipient</strong></td>
<td class='text-left'><strong>Award</strong></td>
</thead>";
    while ($row = $sth->fetch()) {
        extract($row);
        echo "<tr>";
        echo "<td ><a href='edit_person.php?id=$id_person'>$name_person</a></td>";
        echo "<td class='text-left'><a href='list.php?award=$id_award'>$name_award</a></td>";
        echo "</tr>";
    }
    echo "</table><p>";
    echo "</div><!-- ./col-md-8 --></div><!-- ./row -->"; //close out list and open divs
}


/* footer.php will close the db connection */
?>
</div>
