<?php

    // configuration
    require("../includes/config.php");

    // Note: can't use ROOTDIR here because ROOTDIR is set in constants.php
    // render portfolio
    render("site.php", ["title" => "Campground Information"]);

?>
