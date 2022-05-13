<?php

    // configuration
    require("../includes/config.php");

    // Note: can't use ROOTDIR here because ROOTDIR is set in constants.php
    // render portfolio
    render("delete_person_award.php", ["title" => "Delete an Award"]);
