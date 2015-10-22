<?php

    /**
     * functions.php
     *
     * Computer Science 50
     * Problem Set 7
     *
     * Helper functions.
     */

    require_once("constants.php");

    /**
     * Apologizes to user with message.
     */
    function apologize($message)
    {
        render("apology.php", ["message" => $message]);
        exit;
    }

    /**
     * Facilitates debugging by dumping contents of variable
     * to browser.
     */
    function dump($variable)
    {
        require(ROOTDIR."/templates/dump.php");
        exit;
    }

    /**
     * Logs out current user, if any.  Based on Example #1 at
     * http://us.php.net/manual/en/function.session-destroy.php.
     */
    function logout()
    {
        // unset any session variables
        $_SESSION = [];

        // expire cookie
        if (!empty($_COOKIE[session_name()]))
        {
            setcookie(session_name(), "", time() - 42000);
        }

        // destroy session
        session_destroy();
    }

    /*
     * Creates a database connection using the read_only account
     */
    function open_db_browse(){
        $connection =  mysqli_connect (SERVER,USERNAME,PASSWORD,DATABASE)
                       or die ("message");
        return $connection;
    }

    /**
     * Redirects user to destination, which can be
     * a URL or a relative path on the local host.
     *
     * Because this function outputs an HTTP header, it
     * must be called before caller outputs any HTML.
     */
    function redirect($destination)
    {
        // handle URL
        if (preg_match("/^https?:\/\//", $destination))
        {
            header("Location: " . $destination);
        }

        // handle absolute path
        else if (preg_match("/^\//", $destination))
        {
            $protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
            $host = $_SERVER["HTTP_HOST"];
            header("Location: $protocol://$host$destination");
        }

        // handle relative path
        else
        {
            // adapted from http://www.php.net/header
            $protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
            $host = $_SERVER["HTTP_HOST"];
            $path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
            header("Location: $protocol://$host$path/$destination");
        }

        // exit immediately since we're redirecting anyway
        exit;
    }

    /**
     * Renders template, passing in values.
     */
    function render($template, $values = [])
    {

        if ($template=="main.php")
        {
            // extract variables into local scope
            extract($values);

            // render header
            require(ROOTDIR."/templates/header_main.php");

            // render template
            require(ROOTDIR."/templates/$template");

            // render footer
            require(ROOTDIR."/templates/footer.php");
        }    // if template exists, render it
        elseif (file_exists(ROOTDIR."/templates/$template"))
        {
            // extract variables into local scope
            extract($values);

            // render header
            require(ROOTDIR."/templates/header.php");

            // render template
            require(ROOTDIR."/templates/$template");

            // render footer
            require(ROOTDIR."/templates/footer.php");
        }
        // else err
        else
        {
            trigger_error("Invalid template: $template", E_USER_ERROR);
        }
    }

    /* Test to see if user is logged in.  Until login is functional, assume true.
     *
     */
    function is_logged_in() {
        return true;
        //return false;
/*        if ($_SESSION != NULL) {
            return true;
        }
        else return false;
*/
    }
    
    /*
     * Returns the id of the webuser who is making the current change.
     * TODO: expand to also return role, and then need to modify update_query() 
     */
    function get_webuser() {
        return (1);
        //TODO: Return the id_webuser of the person/account making the change
    }
    
    /*
     * Runs the update $query using database connection $cxn, and then logs 
     * a copy of the update query to the transaction log.
     * TODO: query cleaning?
     */
    function update_query($cxn,$query){
        if  (mysqli_query($cxn, $query)) {
            //echo "Record updated successfully";
            $log = "INSERT INTO Transaction_Log VALUES ('',NOW(),"
                    . get_webuser()
                    . ",0,'"
                    . addslashes($query) . "')";
            // echo "<p>Updating the transaction log with: " . $log;
            $result = mysqli_query($cxn, $log);
        } else {
            return mysqli_error($cxn);
        }   
        return 1;
    }

    // TODO: new function, sanitize
    
    /*
     * Exits the script, but only after displaying the footer.  
     * Allows for more graceful exits.
     */
    function exit_with_footer(){
        require(ROOTDIR."/templates/footer.php");
        exit();
    }