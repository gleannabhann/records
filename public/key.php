<?php

    // configuration
    require("../includes/config.php");



    // if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    /* open db connection
     * normally header.php or header_main.php would have done this for us
     * however, neither of those has been included here
     * so we need to do it manually */
    $cxn = open_db_browse();

    // check to make sure this email isn't being used by another user
    $query = "SELECT * FROM WebUsers WHERE email_webuser=:email";
    $data = [':email' => $email];
    $sth = $cxn->prepare($query);
    try {
        $sth->execute($data);
        if ($sth->rowCount()>0) {
            throw new Exception("Email Address Already Exists in System");
        }
    } catch (PDOException $e) {
        if (DEBUG) {
            $message = $e->getMessage();
            $code = (int)$e->getCode();
            throw new Exception("Problem checking for existing email addresses. $message / $code");
        } else {
            throw new Exception("Encountered a problem checking for existing email addresses. Try again?");
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        render("key_form.php", ["title" => "Generate A Registration Key", 'error' => $error]);
        exit;
    }


    // create a DateTime object with the current date & time
    $date = new DateTime();

    // add time in days to date to set expiration of key INV_EXP set in
    // includes/constants.php. We'll then set the time to 23:59.
    $date->add(new DateInterval(INV_EXP));
    $date->setTime(23, 59);

    // format expiration date for db
    // like 2022-01-01 15:45:24
    $expires = $date->format('Y-m-d H:i:s');

    // format expiration date for displaying
    // like 'Monday, January 1st, 2022 at 3:45:24 pm (America/Chicago)
    $expires_friendly = $date->format('l, F jS, Y \a\t h:i:s a (e)');

    // shuffle and truncate key ingredients
    $expires_key = substr(str_shuffle($expires), 0, 5);
    $email_key = substr(str_shuffle($email), 0, 5);
    $salt_key = substr(str_shuffle(KEYSALT), 0, 5);

    // generate a key
    $key = bin2hex($expires_key . $salt_key . $email_key);

    // query to delete any pre-existing invitations for this email address
    $rem_query = "DELETE FROM invites WHERE invite_email=:email";
    $rem_data = [':email' => $email];

    // query to insert new invitation for this email address
    $add_query = "INSERT INTO invites (invite_email, invite_key, invite_expires) VALUES (:email, :key, :expires)";
    $add_data = [':email' => $email, ':key' => $key, ':expires' => $expires];
    try {
        // delete old invites
        $sth = $cxn->prepare($rem_query);
        $sth->execute($rem_data);
        // add new invite
        $sth = $cxn->prepare($add_query);
        $sth->execute($add_data);
        if ($sth->rowCount()==0) {
            throw new Exception("Failed to record invitation.");
        }
    } catch (PDOException $e) {
        if (DEBUG) {
            $error = $e->getMessage;
            $code = (int)$e->getCode;
            $message = "Error inserting invite key into database. Message: $error / Code: $code";
        } else {
            $message = "Error generating invite. Please try again.";
        }
        render("key_form.php", ["title" => "Generate A Registration Key", "error" => $message]);
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
        render("key_form.php", ["title" => "Generate A Registration Key", "error" => $error]);
        exit;
    }
    render("key_result.php", ["title" => "Generated Key", "key" => $key, "email" => $email, 'expires' => $expires_friendly]);
    exit;
} else {
    render("key_form.php", ["title" => "Generate A Registration Key"]);
    exit;
}

/* close the db
 * normally handled by footer.php
 * however, we are not including that file here
 * so we need to close it manually */
$cxn = null;
