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
            if (crypt($_POST["password"] . SSALT) == $row["password"])
            {
                // remember that user is now logged in by storing user's ID in session
                $_SESSION["id"] = $row["id"];


              // TODO: insert additional $_SESSION variables for determining what permissions the user has
              // ie $_SESSION["herald"] = $row["herald"] (stores a TRUE/FALSE value)
              // $_SESSION["marshal_rapier"] = $row["marshal_rapier"] (stores a TRUE/FALSE value)
              // etc, etc, so we can test against these variables to determine if a user
              // should be able to modify the data

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
