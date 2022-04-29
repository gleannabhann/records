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
    // Temporarily disabled since we don't have the email running yet
    // TODO make sure we get this or something like it working.
    // suggestion: generate an SHA1sum using the email address + some salt
    // and email the generated SHA1sum to the email address as the key
    // Then we can regen the SHA1sum using the salt to compare, and then
    // accept the registration.
    // We need to only allow logged in users with appropriate admin access to
    // use the key.php generator.
    /*
    else if ($_POST["key"] != crypt($_POST["email"]))
    {
        apologize("You entered an invalid registration key!");
        exit;
    }
     */
    else
    {
      // connect to the db
      $cxn = open_db_browse() or die ("message");

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
      try 
      {
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
          if (DEBUG)
          {
            echo "\nPDOStatement::errorInfo():\n";
            $arr = $sth->errorInfo();
            echo $e->getMessage();
            echo $e->getCode();
            print_r($arr);
          }
      } 
        // if the value of $errors has been changed, show user the error message
      if ($errors == true) {
        // TODO after you add a username collision check before the insert
        // query, change this error message.
        apologize("Something went wrong with your registration. The username you entered may already be in use. Try a different username\n");
        if (DEBUG)
          echo "\nPDOStatement::errorInfo():\n";
          $arr= $sth->errorInfo();
          print_r($arr);
      } else {
          if (DEBUG) {
            echo "<h1>Got past query</h1>"; 
          }
          $query ="SELECT LAST_INSERT_ID() AS id";
          $sth = $cxn->prepare($query);
          $sth->execute();
          $result = $sth->fetch();

          // remember that user's now logged in by storing user's ID in session
          $_SESSION["id"] = $result['id'];

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
