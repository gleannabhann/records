<?php

    /**
     * Order of Precedence
     * config.php
     *
     * Configures pages.
     */

    // display errors, warnings, and notices
    ini_set("display_errors", true);
    error_reporting(E_ALL);

    // requirements
    require("constants.php");
    require("functions.php");

    // enable sessions
    session_start();

   // require authentication for most pages
    if (preg_match("{^(edit|add|delete)}", $_SERVER["PHP_SELF"]))
    {
        if (empty($_SESSION["id"]))
        {
            redirect("/index.php");
        }
    }
    

?>
