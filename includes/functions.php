<?php

    /**
     * Order of Precedence
     * functions.php
     *
     * Reusable functions
     *
     * PHP version 7.4
     *
     */

    require_once("constants.php");

    /**
     * Appends exception information to a passed-in string
     *
     * For when you want to diplay a more detailed error message while DEBUG is
     * set to `true`. When passed string $msg, a human-friendly error message,
     * and object $exception, an Exception object, this function returns string
     * $msg with the exception's message and error code appended to the end.
     *
     * @param string $msg the human-friendly message
     * @exception object $exception the exception object created in a catch
     * block. If the catch block does `catch (Exception $e) {..` you will pass
     * `$e`.
     *
     * @return string $msg The passed in string in $msg with the exception's
     * message and code appended to the end in parentheses
     *
     * */
    function add_pdo_exception($msg, $exception)
    {
        $message = $exception->getMessage();
        $code = $exception->getCode();
        $msg = $msg . " ($message / $code)";
        return $msg;
    }

    /**
     * Apologizes to user with message.
     *
     * @param string $message the message you wish to pass
     *
     * @return void - calls the render() function (passes $message along),
     * returns control to the calling document.
     */
    function apologize($message)
    {
        render("apology.php", ["message" => $message]);
        return;
    }

    /**
     * Given a message, returns the message with classed html
     *
     * Bootstrap Alert - compatible with bootstrap 3, 4, and 5
     * accepts any $message and one of the bootstrap alert $level names
     * echoes the alert wrapped in a <div> with the correct classes
     * possible levels (& default bootstrap colors) are:
     * 'success' (green), 'info' (blue), 'warning' (yellow), 'danger' (red)
     * these colors may be different if your bootstrap theme is different
     * and this styling won't work if you don't have Bootstrap installed
     *
     * @param string $message the alert message to display
     * @param string $level the bootstrap alert level name
     *
     * @return void
     */
    function bs_alert($message, $level)
    {
        echo "<div class='alert alert-$level center-block'>";
        echo "<p class='text-center'>$message</p>";
        echo "</div>";
    }



    /**
     * Facilitates debugging by dumping contents of variable
     * to browser.
     *
     * includes a template file to dump the variable
     *
     * @param $variable can be any kind of variable, array, or object
     *
     * @return void
     */
    function dump($variable)
    {
        require(ROOTDIR."/templates/dump.php");
        exit;
    }

    /**
     *  Appends custom-formatted information to error.log
     *
     *
     *  Accepts 4 different optional params and combines them together into
     *  a single array. Converts the array to json, appends a newline char to
     *  the end of the json, and passes the json to php function error_log().
     *
     *  @param string $message any human friendly message
     *  @param array $vars any vars you wish displayed
     *  @param object $e an exception object
     *  @param string $file the file name
     *
     *  @return void
     */
    function log_error($message=null, $vars=null, $e=null, $file=null)
    {
        // TODO add "getFile()" and "getLine()" methods to exception if/then
        $datestamp = date("D M d H:i:s.u Y");
        $arr = [];

        if (isset($e)) {
            $arr['exc'] = ['exc_message' => $e->getMessage(), 'exc_code' => $e->getCode()];
        }
        if (isset($vars)) {
            $arr['vars'] = $vars;
        }

        $log = "[$datestamp] ";
        if (isset($file)) {
            $log .= "[$file] ";
        }
        if (isset($message)) {
            $log .= $message;
        }
        $log .= "json_encode($arr) . \n";
        error_log($log);
    }

    /**
     * Appends a line to a debug log if such a log is set up
     *
     * Only appends line to the debug log if DEBUG = 1 and DEBUG_DEST is set
     * Adds a formatted datestamp. Compiles the passed variables into a single
     * array. Converts the array to json and appends a newline char to the end
     * of the json string. Writes the json + newline to custom debug log file.
     * The webserver user must have write perms on the debug.log file.
     * Recommended location for debug.log file is /var/log/php/debug.log
     *
     * @param string $message a human friendly message for context
     * @param array $vars an array of variables you want included
     * @param object $e an exception object
     * @param string $file a filename (intended to be the name of the calling
     * file, but you are free to pass any string you like here)
     *
     * @return void
     * */
    function log_debug($message=null, $vars=null, $e=null, $file=null)
    {
        // TODO add "getFile()" and "getLine()" methods to exception if/then
        if (DEBUG) {
            if (null !== DEBUG_DEST) {
                $datestamp = date("D M d H:i:s.u Y");
                $arr = [];
                $arr['date'] = $datestamp;
                if (isset($message)) {
                    $arr['message'] = $message;
                }
                if (isset($e)) {
                    $arr['exc'] = ['exc_message' => $e->getMessage(), 'exc_code' => $e->getCode()];
                }
                if (isset($vars)) {
                    $arr['vars'] = $vars;
                }
                $log = json_encode($arr) . "\n";
                error_log($log, 3, DEBUG_DEST);
            }
        }
    }

/* *
 * Uses email option of error_log() to generate an email to the developers to
 * alert them about critical errors.
 *
 * This function can be used as an early-warning method by calling it in the
 * catch block of any try/catch wrapping a database call that does not rely on
 * user input - if that try fails, we know something is wrong with our
 * database connection. If an email address is not passed in, this function
 * defaults to the address set in EMAIL_DEST.
 *
 * @param string $message any human-friendly message
 * @param array $vars an array containing any variables you want included. Be
 * sure to strip any PII out of the variables before you pass the array in.
 * @param object $e an exception object
 * @param string $file a reference file name
 * @param string $email a specific email destination address
 *
 * @return void
 * */

    function email_error($message=null, $vars=null, $e=null, $file=null, $email=EMAIL_DEST)
    {
        // TODO add "getFile()" and "getLine()" methods to exception if/then
        $datestamp = date("D M d H:i:s.u Y");
        $headers = "From: ADMIN_EMAIL\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=ISO-8859-1";
        if (!isset($file)) {
            $file = $_SERVER['REQUEST_URI'];
        }
        $subj = "Error on $file\n$headers";
        $log = "<p>This message was automatically generated from $file at $datestamp. </p>";
        $log .= "<p>Is DEBUG set? " . (boolval(DEBUG) ? 'True' : 'False') . "</p>";
        if (isset($message)) {
            $log .= "<p>Message: $message</p>";
        } else {
            $log .= "<p>Message: (no message included)</p>";
        }
        if (isset($e)) {
            $e_msg = $e->getMessage();
            $e_code = $e->getCode();
            $log .= "<p>Exception information:</p><p>File: $file</p><p>Message: $e_msg<br/>Code: $e_code</p>";
        } else {
            $log .= "<p>Exception info not provided.</p>";
        }
        if (isset($vars)) {
            $arr = json_encode(
                $vars,
                JSON_UNESCAPED_SLASHES |
             JSON_UNESCAPED_UNICODE |
             JSON_PRETTY_PRINT |
             JSON_PARTIAL_OUTPUT_ON_ERROR |
             JSON_INVALID_UTF8_SUBSTITUTE
            );
            $log .= "<p>Vars:<br/>$arr</p>";
        } else {
            $log .= "<p>Vars not provided</p>";
        }

        error_log($log, 1, $email, $subj);
    }



    /**
     * Logs out current user, if any.
     *
     * Based on Example #1 at
     * http://us.php.net/manual/en/function.session-destroy.php.
     *
     * @param void
     *
     * @return void
     */
    function logout()
    {
        // unset any session variables
        $_SESSION = [];

        // expire cookie
        if (!empty($_COOKIE[session_name()])) {
            setcookie(session_name(), "", time() - 42000);
        }

        // destroy session
        session_destroy();
    }

    /*
     * Creates a database connection using the account details set in
     * constants.php
     *
     * Logs failures to the system error log.
     *
     * @param void
     *
     * @return object $connection a PDO connection with default options set
     */
    function open_db_browse()
    {
        $host = SERVER;
        $db = DATABASE;
        $charset = 'utf8mb4';
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
          PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $connection =  new PDO($dsn, USERNAME, PASSWORD, $options);
        } catch (PDOException $e) {
            $msg = "DB Connection Failure.";
            $vars = ['exc_msg' => $e->getMessage(), 'exc_code' => $e->getCode()];
            $arr = ['message' => $msg, 'vars' => $vars];
            $message = json_encode($arr) . "\n";
            error_log($message);
        }
        return $connection;
    }

    /**
     * Redirects user to destination, which can be
     * a URL or a relative path on the local host.
     *
     * Because this function outputs an HTTP header, it
     * must be called before caller outputs any HTML.
     *
     * @param string $destination where you're redirecting the user
     *
     * @return void
     */
    function redirect($destination)
    {
        // handle URL
        if (preg_match("/^https?:\/\//", $destination)) {
            header("Location: " . $destination);
        }

        // handle absolute path
        elseif (preg_match("/^\//", $destination)) {
            $protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
            $host = $_SERVER["HTTP_HOST"];
            header("Location: $protocol://$host$destination");
        }

        // handle relative path
        else {
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
     *
     * This function builds a page using standard parts:
     * - The header template file
     * - The body template file
     * - The footer template file
     *
     * Accepts any number of variable values as an array.
     *
     * @param string $template the name of the template file
     * @param array $values any number of named key-pair values
     *
     * @return void
     */
    function render($template, $values = [])
    {
        if ($template=="main.php") {
            // extract variables into local scope
            extract($values);

            // render header
            require(ROOTDIR."/templates/header_main.php");

            // render template
            require(ROOTDIR."/templates/$template");

            // render footer
            require(ROOTDIR."/templates/footer.php");
        }    // if template exists, render it
        elseif (file_exists(ROOTDIR."/templates/$template")) {
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
        else {
            trigger_error("Invalid template: $template", E_USER_ERROR);
        }
    }

    /* Returns permissions level for $role
     *
     * 0 means you can read the public data only
     * 1 means you can read the records data
     * 2 means you can read and add to the records
     * 3 means you can read, add, and update the records
     * 4 means you can read, add, and update records, as well as read userdata.
     * 5 means you can invite new users in addition to everything 4 can do.
     * 6 means you can do everything 5 can do and you can edit userdata
     *
     * @param string $role the name of a role that exists in column `name_role` the db table
     * `Roles` - which is set in a logged-in user's session.
     *
     * @return int $perm the perm level associated with the role name in the
     * logged-in user's session.
     */

    function permissions($role)
    {
        $perm =0;
        if (is_logged_in()) {
            if (isset($_SESSION["Admin"])) {
                $perm=$_SESSION["Admin"];
            }
            if (isset($_SESSION[$role])  && (is_numeric($_SESSION[$role]))) {
                return max($_SESSION[$role], $perm);
            } else {
                return $perm;
            }
        }
        return 0;
    }
    /* Test to see if user is logged in.
     *
     * This function is unused in this app and is a candidate for removal
     * @deprecated
     *
     * @return bool
     *
     */
    function is_logged_in()
    {
        return isset($_SESSION["id"]);
        //return false;
/*        if ($_SESSION != NULL) {
            return true;
        }
        else return false;
*/
    }

    /*
     * Returns the id of the webuser
     *
     * Used by function update_query() to insert the id of the logged-in user
     * who is making the change to the database.
     * TODO: expand to also return role, and then need to modify update_query()
     *
     * @param void
     *
     * @return int the logged-in user's id
     */
    function get_webuser()
    {
        return $_SESSION["id"];
        //TODO: Return the id_webuser of the person/account making the change
    }

    /*
     * Update and log db query
     *
     * Runs the update $query using database connection $cxn, and then logs
     * a copy of the update query to the transaction log.
     * TODO: query cleaning?
     * TODO: option for preparing via bind_param() etc if special handling is
     * needed
     *
     * If the log-transaction portion fails, it silently logs a copy of what
     * would have been logged to the system apache error log.
     *
     * @param object $cxn the database connection
     * @param string $query the MySQL query to use
     * @param array|list $data (optional) an array of key-value pairs matching named
     * placeholders in the query, -OR- a list of variables matching the order
     * of `?` placeholders in the query.
     *
     * @return bool
     */
    function update_query($cxn, $query, $data=null)
    {
        $sth = $cxn->prepare($query);
        if (isset($data)) {
            $sth->execute($data);
        } else {
            $sth->execute();
        }
        if ($sth->rowCount() != '0') {
          // one or more rows affected, let's log the transaction
            $log_query = "INSERT INTO Transaction_Log VALUES (NULL,NOW(),"
                       . " :web_user ,0, :query)";
            // we want to include the id of the logged-in user who initiated
            // the update
            $webuser = get_webuser();
            // we're converting the variables to json here to make them easy to
            // read in the database
            $query = $query . " Vars: " . json_encode($data);
            $log_data = [':web_user' => $webuser, ':query' => $query];
            try {
                $sth_log = $cxn->prepare($log_query);
                $sth_log->execute($log_data);

                // echo "<p>Updating the transaction log with: " . $log;
            } catch (PDOException $e) {
                // in the event that logging the transaction to the database
              // fails, we will fall back to logging it in the apache error log
              // instead
                $message = $e->getMessage();
                $code = (int)$e->getCode();
                // send the information to the error log
                $msg = "functions.php/update_query() tx log failure";
                $vars = ['log_query' => $log_query, 'log_data' => $data, 'exc_msg' => $message, 'exc_code' => $code];
                $arr = ['message' => $msg, 'vars' => $vars];
                $err = json_encode($arr);
                error_log($err);
                // TODO add administrator notification by email since if this
                // is failing, someone needs to look at it as soon as possible
            }
        }
        return 1;
    }


    /* Sanitizes a string to insert into mysql.
     *  - trims extra spaces
     *  - escapes special characters
     *
     * @param string $str the string to sanitize
     *
     * @return string $str the sanitized string
     *
     * @deprecated this function may not be needed in the future as all queries
     * are being converted to use PDO prepared statements with `?` or named
     * placeholders in the query string and all variables passed separately in
     * an array or list, as appropriate.
     *
     */
    function sanitize_mysql($str)
    {
        $str = trim($str);
        $str = addslashes($str);
        return $str;
    }

   /* takes a Street Address and returns a corresponding
    * latitude and Longitude.
    * Has built in failure checks
    *
    * @param string $address a street address formatted for the Google Maps API
    *
    * @return array|bool returns an array.
    *
    * <code>
    * Example:
    * <?php
    * $result = geocode($address);
    * $latitude = $result[0];
    * $longitude = $result[1];
    * ?>
    * </code>
    *
    */
   function geocode($address)
   {
      // grab the API key
      if (defined("MAPSAPI")) {
        $key = constant("MAPSAPI");
      } else {
        $key = null;
      }

      // url encode the address
       $address = urlencode($address);

       // google map geocode api url
       $url = "https://maps.googleapis.com/maps/api/geocode/json?sensor=false&address={$address}&key=$key";

       // get the json response
       $geocode_json = file_get_contents($url);

       // decode the json
       $geocode = json_decode($geocode_json, true);

       // response status will be 'OK', if able to geocode given address
       if ($geocode['status']=='OK') {

          // get the important data
           $lat = $geocode['results'][0]['geometry']['location']['lat'];
           $lng = $geocode['results'][0]['geometry']['location']['lng'];


           // verify if data is complete
           if ($lat && $lng) {

              // put the data in the array
               $coords = [];

               array_push(
                   $coords,
                   $lat,
                   $lng
               );

               return $coords;
           } else {
               return false;
           }
       } else {
           return false;
       }
   }
    /** Refreshes the session or logs the user out
     *
     * Checks time stamps in the session variables to ensure that the session
     * isn't too old, and resets the UPDATE variable for the inactivity timeout
     * runs logout() if inactivity timeout or lifetime expiration are exceeded.
     *
     * @return bool
     */
   function validate_session()
   {
       if (isset($_SESSION['CREATED'])) {
           // if the session creation date stamp is older than 7 days, destroy the session
           $time_creation = time() - $_SESSION['CREATED'];
           $time_updated = time() - $_SESSION['UPDATED'];
           $time_refreshed = time() - $_SESSION['REFRESHED'];
           if (DEBUG) {
               //echo "Time since creation". $time_creation . "(604800)<br/>";
          //echo "Time since update". $time_updated. "(7200)<br/>";
          //echo "Time since refresh". $time_refreshed. "(1800)<br/>";
           }
           if ((time() - $_SESSION['CREATED'] > MAX_SESSION) || (isset($_SESSION['UPDATED']) && (time() - $_SESSION['UPDATED'] > MAX_INACTIVE))) {
               // log out current user, if any
               logout();
               // redirect user
               redirect("/");
           } else {
               $_SESSION['UPDATED'] = time(); // update last activity time stamp
           }
           if (time() - $_SESSION['REFRESHED'] > SESSION_REFRESH) {
               // session last refreshed more than 30 minutes ago
         session_regenerate_id(true);    // change session ID for the current session and invalidate old session ID
         $_SESSION['REFRESHED'] = time();  // update creation time
           }
       }
       return 1;
   }
    /*
     * Exits the script, but only after displaying the footer.
     * Allows for more graceful exits.
     *
     * Useful when a database operation fails and there's not point in
     * continuing on in the script. This skips the remainder of the document
     * but inserts the footer template on its way out.
     *
     * @return void
     */
    function exit_with_footer()
    {
        require(ROOTDIR."/templates/footer.php");
        exit();
    }

   /**
    * Produces a string output to create a button in html
    *
    * @todo accept classes for both the anchor and the button
    *
    * <code>
    * <a href="somepage.html"><button type="button">Text of Some Page</button></a>
    * </code>
    *
    * @param string $link the destination url
    * @param string $label what the button should say
    *
    * @return string html composed of a button enclosed in an anchor
    */
    function button_link($link, $label)
    {
        return '<a href="'.$link.'">'
                .'<button type="button">'
                .$label
                .'</button></a>';
    }


    /**
     * Generates an html hyperlink from provided url and label text
     *
     * @todo accept a class string for the anchor
     *
     * @param string $link a url, relative or otherwise
     * @param string $label text for the link
     *
     * @return string an html anchor
     *
     */
    function live_link($link, $label)
    {
        return "<a href='$link'>$label</a>";
    }


    /**
     * Wraps the supplied string in a second-level html header block.
     * Useful for synchronizing across web pages
     *
     * @param string $label your header text
     *
     * @return string your header text wrapped in
     * an html header block
     *
     */
    function form_title($label)
    {
        return '<h2>'.$label.'</h2>';
    }


    /**
     * Wraps the supplied string in a third-level html header block
     *
     * @param string $label your sub-header text
     *
     * @return string your sub-header text wrapped in a header block
     */
    function form_subtitle($label)
    {
        return '<h3>'.$label.'</h3>';
    }

    /**
     * Wraps the supplied string in line breaks and bold format tags
     *
     * @param string $label your sub-subheader text
     *
     * @return string your sub-subheader text, but formatted
     */
    function form_subsubtitle($label)
    {
        return '<br><b>'.$label.'</b></br>';
    }

    /**
     * Converts an image blob to a displayable html image tag
     *
     * @param string $image the image blob in base64
     * @param string $ftype the filetype (png, gif, jpeg, or jpg)
     * @param int|string $width the width in any format accepted by the width
     * attribute of an html <img> tag
     * @param string $alt the alt-text for the image
     * @param string $title the title for the image
     *
     * @return void / echoes the html directly
     *
     */
    function display_image($image, $ftype, $width, $alt = " ", $title = " ")
    {
        if ($image !== false) {
            switch ($ftype) {
            case "image/png"  : echo '<img src="data:image/png;base64,' . $image  . '"  width='.$width.' alt="'.$alt.'" title="'.$title.'" />';
                break;
            case "image/gif"  : echo '<img src="data:image/gif;base64,' . $image  . '"  width='.$width.' alt="'.$alt.'" title="'.$title.'" />';
                break;
            case "image/jpeg" : echo '<img src="data:image/jpeg;base64,' . $image  . '"  width='.$width.' alt="'.$alt.'" title="'.$title.'" />';
                break;
            case "image/jpg"  : echo '<img src="data:image/jpg;base64,' . $image  . '"  width='.$width.' alt="'.$alt.'" title="'.$title.'" />';
                break;
            default:
                echo "No image";
            }
        }
    }
