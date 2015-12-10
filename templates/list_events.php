<?php

    // configuration
    require("../includes/config.php");

    // Note: can't use ROOTDIR here because ROOTDIR is set in constants.php
    // render portfolio
    render("list_events.php", ["title" => "Events"]);

?>
