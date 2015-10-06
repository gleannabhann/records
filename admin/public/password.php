<?php

    // configuration
    require("../includes/config.php"); 

    // if form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // validate submission
        if (empty($_POST["cur_pass"]))
        {
            apologize("You must enter your current password.");
            exit;
        }
        else if (empty($_POST["new_pass"]))
        {
            apologize("You must enter your new password.");
            exit;
        }
        else if (empty($_POST["confirmation"]))
        {
            apologize("You must re-enter your new password to confirm");
            exit;
        }
        else
        {
            // query database for user
            $rows = query("SELECT * FROM users WHERE id = ?", $_SESSION["id"]);

            // if we found user, check password
            if (count($rows) == 1)
            {
                // first (and only) row
                $row = $rows[0];

                // compare hash of user's input against hash that's in database
                if (!(crypt($_POST["cur_pass"], $row["hash"]) == $row["hash"]))
                {
                    apologize("You entered your current password incorrectly");
                    exit;
                }
                // compare new password against confirmation
                else if ((strcmp($_POST["new_pass"], $_POST["confirmation"])) != 0)
                {
                    apologize("Your new password does not match the confirmation");
                    exit; 
                }
                else
                {
                query("UPDATE users SET hash = ? WHERE id = ?", (crypt($_POST["new_pass"])), $_SESSION["id"]);
                }
            }
            
        render("password-confirm.php", ["title" => "Password Changed!"]);
        
        }
    }
    else
    {
        // else render form
        render("password-form.php", ["title" => "Change Your Password"]);
    }

?>
