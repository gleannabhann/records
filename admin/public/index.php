<?php

    // display errors, warnings, and notices
    ini_set("display_errors", true);
    error_reporting(E_ALL);

    

    // configuration
    require("../includes/config.php");

    // render main page
    render("main.php", ["title" => ""]);

?>
