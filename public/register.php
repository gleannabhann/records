<?php

// configuration
require("../includes/config.php");

// if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        apologize("Please enter a username");
        exit;
    } elseif (empty($_POST["password"])) {
        apologize("Please enter a password");
        exit;
    } elseif ($_POST["password"] != $_POST["confirmation"]) {
        apologize("Your password and confirmation don't match!");
        exit;
    } elseif (!isset($_POST["email"])) {
        apologize("Please enter an email address");
        exit;
    } else {

    // if there's an existing account logged in, log it out
        if (isset($_SESSION)) {
            logout();
        }
        /* connect to database
         * normally handled by header.php or header_main.php
         * but this document does not include either one */
        try {
            $cxn = open_db_browse();
        } catch (PDOException $e) {
            if (DEBUG) {
                $message = $e->getMessage();
                $code = (int)$e->getCode();
                $error = "Could not establish database connection. $message / $code";
            } else {
                $error = "Could not establish database connection.";
            }
            die($error);
        }
        // pull email var
        $email = $_POST['email'];
        $username = $_POST['username'];

        // check for active, unused invite code
        $query = "SELECT * FROM invites WHERE invite_email=:email AND invite_used=0";
        $data = [':email' => $email];
        $sth = $cxn->prepare($query);
        try {
            $sth->execute($data);
        } catch (PDOException $e) {
            if (DEBUG) {
                $message = $e->getMessage();
                $code = $e->getCode();
                $error = "Database Error! $message / $code";
                echo $error;
                exit_with_footer;
            } else {
                echo "I couldn't fetch invitations.";
                exit_with_footer();
            }
        }
        // make sure there is an invitation in the system
        if ($sth->rowCount()==0) {
            apologize("I couldn't find your invitation.");
            exit_with_footer();
        }
        $invitation = $sth->fetch(PDO::FETCH_ASSOC);
        extract($invitation);
        // make sure the invitation hasn't expired
        $invite_expires = DateTime::createFromFormat('Y-m-d H:i:s', $invite_expires);
        $now = new DateTime();
        $valid = ($invite_expires > $now);
        if ($valid == 0) {
            apologize("It looks like your invitation has expired.");
            exit_with_footer();
        }
        // make sure the invitation keys match

        if ($invite_key != $_POST['key']) {
            apologize("Your invitation key doesn't match what we have on file");
        }

        // check for username collisions in the WebUsers table
        $query = "SELECT name_webuser FROM WebUsers WHERE name_webuser=:username";
        $data = [':username' => $username];
        $sth = $cxn->prepare($query);
        $sth->execute($data);
        if ($sth->rowCount() > 0) {
            apologize("The username you chose is already in use. Please choose a different one.");
        }

        // the method of password verification changed when this app was upgraded
        // to PHP 7.4. If you are getting database errors and you didn't upgrade
        // your database schema to include the new table fields, you need to do
        // that first:
        /*
        ALTER TABLE WebUsers
          ADD COLUMN `hash_webuser` CHAR(255) DEFAULT NULL,
          ADD COLUMN `last_logged_in` TIMESTAMP DEFAULT NULL;
         */
        $hash = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $date_time = date("Y-m-d H:i:s"); // date & time in MySQL DATETIME format

        // create the insertion query
        $query = "INSERT INTO WebUsers VALUES (NULL, :name_webuser, 'none', :name_mundane_webuser, :email_webuser, :id_person, :hash_webuser, :last_logged_in)";
        $values = [':name_webuser' => $_POST['username'], ':name_mundane_webuser' => $_POST['mundaneName'], ':email_webuser' => $_POST['email'], ':id_person' => $_POST['personId'], ':hash_webuser' => $hash, ':last_logged_in' => $date_time];

        // set up user-facing error reporting
        $errors = false;

        // TODO add a query that checks for username collisions and throws
        // a custom exception if one is found
        try {
            $sth = $cxn->prepare($query);
            // try to add the user to the database
            $sth->execute($values);

            // this should give us an exact count of 1 if the Insert statement was
            // successful, so let's check for that.
            $result = $sth->rowCount() ;
            if ($result != 1) {
                $errors = true;
                if (DEBUG) {
                    echo "No rows Affected";
                }
            }
        } catch (PDOException $e) {
            // something went wrong during either execute() or rowCount()
            $errors = true;
            if (DEBUG) {
                echo "\nPDOStatement::errorInfo():\n";
                $arr = $sth->errorInfo();
                echo $e->getMessage();
                echo $e->getCode();
                print_r($arr);
            }
        }
        // if the value of $errors has been changed, show user the error message
        if ($errors == true) {
            apologize("Something went wrong with your registration.");
            if (DEBUG) {
                echo "\nPDOStatement::errorInfo():\n";
            }
            $arr= $sth->errorInfo();
            print_r($arr);
        } else {
            if (DEBUG) {
                echo "<h1>Got past query</h1>";
            }
            // update the invitation to show it has been used
            $query = "UPDATE invites SET invite_used=1 WHERE invite_email=:email";
            $data = [':email' => $email];
            $sth = $cxn->prepare($query);
            $sth->execute($data);
            if ($sth->rowCount()==0) {
                error_log("Couldn't update invitation to 'used' where 'invite_email' = $email");
                if (DEBUG) {
                    echo "Couldn't update invitation to 'used' where 'invite_email' = $email";
                }
            }
            // get the newly minted user's new user id
            $query ="SELECT LAST_INSERT_ID() AS id";
            $sth = $cxn->prepare($query);
            $sth->execute();
            $result = $sth->fetch();
            $id = $result['id'];
            $username = $_POST['username'];

            session_start();
            if (!isset($_SESSION['initiated'])) {
                session_regenerate_id();
                $_SESSION['initiated'] = true;
            }

            // remember that user is now logged in by storing user's ID in session
            $_SESSION["id"] = $id;
            $_SESSION["webuser_name"] = $username;

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
            . "AND id_webuser = :id_webuser ";

            $query_data = [':id_webuser' => $id];
            // Set permissions in $_SESSION: indexed by name of role, value is level of permission
            // Note that only permissions that haven't expired yet are included.
            // In query, the expire_role and id_roletype were included for debugging only.
            // TODO: make sure that Roletype occurs only once.
            $max_perm=0;
            $sth = $cxn->prepare($query);
            $sth->execute($query_data);
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

            /* close db connection
             * normally handled by footer.php, but we aren't including it
             */
            $cxn = null;

            // redirect to main page



            // redirect to main
            redirect("/");
        }
    }
} else {
    // else render form
    render("register_form.php", ["title" => "Register"]);
}
