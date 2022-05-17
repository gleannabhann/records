<?php

// configuration
require("../includes/config.php");
// if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // validate submission
    if (empty($_POST["username"])) {
      $values = ['message' => "You must provide your username", 'alert' => 'warning'];
      render("apology.php", $values);
      exit();
    } elseif (empty($_POST["password"])) {
      $values = ['message' => "You must provide your password.", 'alert' => 'warning'];
      render("apology.php", $values);
      exit();
    }

    $username = $_POST["username"];
    $password = $_POST["password"];

    /* establish database connection object
     * normally header.php or header_main.php would handle this
     * however this document does not include either of those
     * so we need to open the db connection manually */
    try {
        $cxn = open_db_browse();
    } catch (PDOException $e) {
        if (DEBUG) {
            $message = $e->getMessage();
            $code = $e->getCode();
            $error = "Could not establish database connection. $message / $code";
        } else {
            $error = "Could not establish database connection. Please contact your administrator.";
        }
        die($error);
    }
    // pre-build the query
    $query = "select * from WebUsers where name_webuser = :username";
    $data = [':username' => $username];

    // query database for user
    $sth = $cxn->prepare($query);
    $sth->execute($data) or die("couldn't execute query" . $query);
    $rowcount = $sth->rowCount();

    // pre-set the verified flag
    $verified = false;

    // escape plan for the user info
    $id = null;
    $name = null;

    // if we found user, check password
    if ($rowcount == 1) {
        // first (and only) row
        while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            $name = $row['name_webuser'];
            $id = $row['id_webuser'];
            $hash = $row['hash_webuser'];
            // set the pw update query because we can use it for any
            // conditional need to update the user's password
            $pw_update = 'UPDATE WebUsers SET hash_webuser = :hash, last_logged_in = :date_time WHERE id_webuser = :id_webuser';

            // set the update query for logging last login date-time without updating
            // the password
            $login_update = "UPDATE WebUsers SET last_logged_in = :date_time WHERE id_webuser = :id_webuser";
            $date_time = date("Y-m-d H:i:s"); //date & time in MySQL DATETIME format
            $id = $row['id_webuser'];
            $data = [':date_time' => $date_time, ':id_webuser' => $id];

            // new, more secure method of verification
            if (password_verify($password, $hash)) {
                // set $verified to true
                $verified = true;

                // check if the pass needs a rehash. If yes, do it, and update
                if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
                    $new_hash = password_hash($password, PASSWORD_DEFAULT);
                    $data[':hash'] = $new_hash;
                    $sth = $cxn->prepare($pw_update);
                    $sth->execute($data);
                    if (DEBUG) {
                        $rows_affected = $sth->rowCount();
                        $msg = "Verified password for User $id using password_verify() at $date_time and user's password needed a re-hash, resulting in " . $rows_affected . " rows affected.";
                        error_log($msg);
                    }
                } else {
                    // rehash not needed, just update the last logged in datestamp
                    $sth = $cxn->prepare($login_update);
                    $sth->execute($data);

                    if (DEBUG) {
                        $rows_affected = $sth->rowCount();
                        $msg = "Verified password for User $id using password_verify() at $date_time. Re-hash not needed. Last login date updated, " . $rows_affected . " rows affected.";
                        error_log($msg);
                    }
                }
                // new method of verification didn't work, let's try the old one
            } elseif (crypt($_POST["password"], SALT) == $row["password_webuser"]) {
                $verified = true;
                $new_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $data[':hash'] = $new_hash;
                $sth = $cxn->prepare($pw_update);
                $sth->execute($data);

                if (DEBUG) {
                    $rows_affected = $sth->rowCount();
                    $msg = "Verified password for User $id using crypt() at $date_time. Re-hash needed, resulting in " . $rows_affected . " rows affected.";
                    error_log($msg);
                }
            }
        }
    }
    if ($verified == true) {
        // regenerate the session_id because we are changing the level of
        // privilege here
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id();
            $_SESSION['initiated'] = true;
        }

        // remember that user is now logged in by storing user's ID in session
        $_SESSION["id"] = $id;
        $_SESSION["webuser_name"] = $name;

        // generate a key based on user_agent, user ID,

        $_SESSION["key"] = rand(0, 999999); // we need to come up with a unique session key
        // built from various bits relevant to the session, such as HTTP_USER_AGENT
        // and potentially also the IP address, though this can cause problems
        // with users browsing via TOR or via an IP pool/load balancer
        // in the mean time, a random number will do.

        $query = "SELECT RoleTypes.id_roletype, name_roletype, name_role, expire_role, perm_role "
            . "FROM Webusers_Roles, Roles, RoleTypes "
            . "WHERE Webusers_Roles.id_role = Roles.id_role "
            . "AND Roles.id_roletype = RoleTypes.id_roletype "
            . "AND expire_role > CURDATE() "
            . "AND id_webuser = :session ";
        $session_id = $_SESSION["id"];
        $session_data = [':session' => $session_id];
        // Set permissions in $_SESSION: indexed by name of role, value is level of permission
        // Note that only permissions that haven't expired yet are included.
        // In query, the expire_role and id_roletype were included for debugging only.
        // TODO: make sure that Roletype occurs only once.
        $max_perm=0;
        $sth = $cxn->prepare($query);
        $sth->execute($session_data);
        while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            //echo "Adding $perm_role to variable $name_roletype";
            $_SESSION[$name_roletype] = $perm_role;
            $_SESSION[$name_role] = $perm_role;
            $max_perm=max($max_perm, $perm_role);
        }
        $_SESSION["Any"]=$max_perm;
        $_SESSION["CREATED"] = time();
        $_SESSION["UPDATED"] = time();
        $_SESSION["REFRESHED"] = time();
        // TODO: set expirations. Need: destroy on browser close; expire after
        // 14 days, if (using a public computer) {expire after 4 hours}
        // based on login form input -> tickbox asking, "Are you using a shared or
        // public computer?". If TRUE, expire after 4 hours, else, expire after 2 weeks
        // redirect to main page
        redirect("/");
    }
    // else pass an error to the apology template
    $values = ['message' => "Invalid username and/or password.", 'alert' => 'danger'];
    render("apology.php", $values);
    exit();
} else {
    // else render form
    render("login_form.php", ["title" => "Log In"]);
}
/* close db connection
 * normally handled by footer.php
 * but we are not including that file here */
$cxn = null;
