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
        // connect to the db
        $cxn = mysqli_connect (SERVER,USERNAME,PASSWORD,DATABASE)
        or die ("message");

        $query = "SELECT * FROM WebUsers WHERE name_webuser = ?", $_POST["username"]);
        //TODO we need to select more data than just the row from WebUsers.
        // We also need all rows from Webusers_Roles that are associated with the user's id

        // query database for user
        $rows = mysqli_query ($cxn, $query)
        or die ("Couldn't execute query");


        // if we found user, check password
        if (count($rows) == 1)
        {
            // first (and only) row
            $row = $rows[0];

            // compare hash of user's input against hash that's in database
            if (crypt($_POST["password"] . SALT) == $row["password_webuser"])
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

                $_SESSION["key"] = NULL // we need to come up with a unique session key
                // built from various bits relevant to the session, such as HTTP_USER_AGENT
                // and potentially also the IP address, though this can cause problems
                // with users browsing via TOR or via an IP pool/load balancer


              // TODO: insert additional $_SESSION variables for determining
              // what permissions the user has
              // ie $_SESSION["herald"] = $row["herald"] (stores a TRUE/FALSE value)
              // $_SESSION["marshal_rapier"] = $row["marshal_rapier"] (stores a TRUE/FALSE value)
              // etc, etc, so we can test against these variables to determine if a user
              // should be able to modify the data.

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
