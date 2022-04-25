<?php

    // configuration
    require("../includes/config.php");
    // if form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // validate submission
        if (empty($_POST["username"]))
        {
            apologize("You must provide your username.");
        }
        else if (empty($_POST["password"]))
        {
            apologize("You must provide your password.");
        }

        $username = $_POST["username"];
        $password = crypt($_POST["password"], SALT);

        if (DEBUG){
         echo "Salted input password: " . $password . "<br/>";
        }
        // connect to the db
        $cxn = open_db_browse() or die ("message");

        // pre-build the query
        $query = "select * from WebUsers where name_webuser = '$username'";

        // query database for user
        $sth = $cxn->prepare($query);
        $sth->execute() or die ("couldn't execute query" . $query);
        $rowcount = $sth->rowCount();

        // if we found user, check password
        if ($rowcount == 1)
        {
            // first (and only) row
          $result = $sth->fetchAll() or die ("couldn't execute query" . $query);
          foreach ($result as $row) {

            //echo $row["password_webuser"];

            // compare hash of user's input against hash that's in database
            if ($password == $row["password_webuser"])
            {

            // regenerate the session_id because we are changing the level of
            // privilege here
            if (!isset($_SESSION['initiated']))
            {
              session_regenerate_id();
              $_SESSION['initiated'] = true;
            }

              // remember that user is now logged in by storing user's ID in session
              $_SESSION["id"] = $row["id_webuser"];
              $_SESSION["webuser_name"] = $row["name_webuser"];

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
                      . "AND id_webuser = ".$_SESSION["id"];
              // Set permissions in $_SESSION: indexed by name of role, value is level of permission
              // Note that only permissions that haven't expired yet are included.
              // In query, the expire_role and id_roletype were included for debugging only.
              // TODO: make sure that Roletype occurs only once.
              $max_perm=0;
              $sth = $cxn->prepare($query);
              $sth->execute();
              foreach ($cxn->query($query) as $row) {
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
          }
        }

        // else apologize
        apologize("Invalid username and/or password.");
    }
    else
    {
        // else render form
        render("login_form.php", ["title" => "Log In"]);
    }

?>
