<?php

    // configuration
    require(BOOTDIR . "/includes/config.php"); 

    // log out current user, if any
    logout();

    // redirect user
    redirect("/");

?>
