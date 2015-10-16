<?php

    // configuration
    require("../includes/config.php");

    // if form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {$hash = crypt($_POST["email"]);
      $email = $_POST["email"];
    render("key_result.php", ["title" => "Generated Key", "hash" => $hash, "email" => $email]);
    exit;
    }

    else{

    render("key_form.php", ["title" => "Generate A Registration Key"]);
    exit;
    }



    ?>
