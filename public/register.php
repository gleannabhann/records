<?php

// configuration
require("../includes/config.php");

// if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    if (empty($_POST["username"]))
    {
        apologize("Please enter a username");
        exit;
    }
    else if (empty($_POST["password"]))
    {
        apologize("Please enter a password");
        exit;
    }
    else if ($_POST["password"] != $_POST["confirmation"])
    {
        apologize("Your password and confirmation don't match!");
        exit;
    }
    else if ($_POST["key"] != crypt($_POST["email"]))
    {
        apologize("You entered an invalid registration key!");
        exit;
    }
    else
    {
      // connect to the db
      $cxn = mysqli_connect (SERVER,USERNAME,PASSWORD,DATABASE)
      or die ("message");
      $hash = crypt($_POST["password"] . SALT);
      // TODO the query below doesn't pass muster per PHP
      // $query = "INSERT INTO `WebUsers`(`name_webuser`, `password_webuser`, `name_mundane_webuser`, `email_webuser`, `id_person`)
      // VALUES ($_POST['username'], $hash, $_POST['mundaneName'], $_POST['email'], $_POST['personId'])";
      $result = mysqli_query ($cxn, $query)
      or die ("Couldn't execute query");

        if ($result === false)
        {
            apologize("Something went wrong with your registration. The username you entered may already be in use. Try a different username\n");
        }
        else
        {
            $query ="SELECT LAST_INSERT_ID() AS id";
            $result = mysqli_query ($cxn, $query)
            or die ("Couldn't execute query");
            $id = $result[0]["id"];
            // remember that user's now logged in by storing user's ID in session
            $_SESSION["id"] = $id;

            // redirect to main
            redirect("/");
        }
    }
}
else
{
    // else render form
    render("register_form.php", ["title" => "Register"]);
}

?>
