<?php

    // configuration
    require("../includes/config.php");

    // Note: can't use ROOTDIR here because ROOTDIR is set in constants.php
    // render portfolio
    render("edit_person_armorial_link.php", ["title" => "Edit a Person\'s Armorial Links"]);
