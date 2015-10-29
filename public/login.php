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

        // echo "Salted input password: " . $password . "<br/>";
        // connect to the db
        $cxn = mysqli_connect (SERVER,USERNAME,PASSWORD,DATABASE)
        or die ("message");

        // pre-build the query
        $query = "select * from WebUsers where name_webuser = '$username'";

        // query database for user
        $rows = mysqli_query ($cxn, $query)
        or die ("Couldn't execute query" . $query);


        // if we found user, check password
        if (count($rows) == 1)
        {
            // first (and only) row
            $row = mysqli_fetch_assoc($rows);

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

              // generate a key based on user_agent, user ID,

              $_SESSION["key"] = rand(0, 999999); // we need to come up with a unique session key
              // built from various bits relevant to the session, such as HTTP_USER_AGENT
              // and potentially also the IP address, though this can cause problems
              // with users browsing via TOR or via an IP pool/load balancer
              // in the mean time, a random number will do.

              $query = "SELECT RoleTypes.id_roletype, name_roletype, expire_role, perm_role "
                      . "FROM Webusers_Roles, Roles, RoleTypes "
                      . "WHERE Webusers_Roles.id_role = Roles.id_role "
                      . "AND Roles.id_roletype = RoleTypes.id_roletype "
                      . "AND expire_role > CURDATE() "
                      . "AND id_webuser = ".$_SESSION["id"];
              // Set permissions in $_SESSION: indexed by name of role, value is level of permission
              // Note that only permissions that haven't expired yet are included.
              // In query, the expire_role and id_roletype were included for debugging only.
              $result = mysqli_query($cxn, $query) or die ("Couldn't execute query");
                while ($row = mysqli_fetch_assoc($result)) {
                    extract($row);
                    //echo "Adding $perm_role to variable $name_roletype";
                    $_SESSION[$name_roletype] = $perm_role;
                }

              // TODO: set expirations. Need: destroy on browser close; expire after
              // 14 days, if (using a public computer) {expire after 4 hours}
              // based on login form input -> tickbox asking, "Are you using a shared or
              // public computer?". If TRUE, expire after 4 hours, else, expire after 2 weeks



                // redirect to main page
                redirect("/");
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
