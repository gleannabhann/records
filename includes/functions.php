<?php

    /**
     * Order of Precedence
     * functions.php
     *
     * Reusable functions
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
    } catch (\PDOException $e) {
      throw new \PDOException ($e->getMessage(), (int)$e->getCode());
    }
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

    /* Returns permissions level for $role
     * 0 means you can read the public data only
     * 1 means you can read the records data
     * 2 means you can read and add to the records
     * 3 means you can read, add, and update the records
     * 4 means you can read, add, and update records, as well as read userdata.
     * 5 means you can invite new users in addition to everything 4 can do.
     * 6 means you can do everything 5 can do and you can edit userdata
     */

    function permissions($role) {
        $perm =0;
        if (is_logged_in()) {
            if (isset($_SESSION["Admin"])) {$perm=$_SESSION["Admin"];}
            if (isset($_SESSION[$role])  && (is_numeric($_SESSION[$role]))) {
                return max($_SESSION[$role], $perm);
            } else {
                return $perm;
            }
        }
        return 0;
    }
    /* Test to see if user is logged in.  Until login is functional, assume true.
     *
     */
    function is_logged_in() {
        return isset($_SESSION["id"]);
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
        return $_SESSION["id"];
        //TODO: Return the id_webuser of the person/account making the change
    }

    /*
     * Runs the update $query using database connection $cxn, and then logs
     * a copy of the update query to the transaction log.
     * TODO: query cleaning?
     */
    function update_query($cxn, $query, $data=null){
      try {
        $sth = $cxn->prepare($query);
        if (isset($data)) {
          $sth->execute($data);
        } else {
          $sth->execute();
        }
      } catch (PDOException $e) {
            $message = $e->getMessage();
            $code = (int)$e->getCode();
            if (DEBUG) {
            throw new Exception("Functions.php::update_query() failed to complete. The query was '$query'. The data was $data. The error message is $message, with code $code");
            } else {
            throw new Exception("Could not Update.");
            }
      }
      if  ($sth->rowCount() != '0') {
            //echo "Record updated successfully";
            $log = "INSERT INTO Transaction_Log VALUES ('',NOW(),"
                  . " :web_user ,0, :query)";
            $webuser = get_webuser();
            $query = addslashes($query);
            $data = [':web_user' => $webuser, ':query' => $query];
            try {
            $sth_log = $cxn->prepare($log);
            $sth_log->execute($data);

            // echo "<p>Updating the transaction log with: " . $log;
            } catch (PDOException $e) {
              $message = $e->getMessage();
              $code = (int)$e->getCode();
              // send the information to the error log
              error_log("Functions.php::update_query() failed to log the transaction. Query was '$query'. Data was '$data'. Error message: $message. Error code: $code.");
              if (DEBUG) {
                throw new Exception("Functions.php::update_query() failed to log the transaction. Query was '$query'. Data was '$data'. Error message: $message. Error code: $code.");
              }
            }
        }        
        return 1;
    }

    // TODO: new function, sanitize
    /* Sanitizes a string to insert into mysql.
     *  - trims extra spaces
     *  - escapes special characters
     */
    function sanitize_mysql($str){
       $str = trim($str);
       $str = addslashes($str);
       return $str;
   }

   /* takes a Street Address and returns a corresponding
    * latitude and Longitude
    * Has built in failure checks
    */
   function geocode($address){

      // url encode the address
      $address = urlencode($address);

      // google map geocode api url
      $url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address={$address}";

      // get the json response
      $geocode_json = file_get_contents($url);

      // decode the json
      $geocode = json_decode($geocode_json, true);

      // response status will be 'OK', if able to geocode given address
      if($geocode['status']=='OK'){

          // get the important data
          $lat = $geocode['results'][0]['geometry']['location']['lat'];
          $lng = $geocode['results'][0]['geometry']['location']['lng'];


          // verify if data is complete
          if($lat && $lng){

              // put the data in the array
              $coords = [];

              array_push(
                  $coords,
                  $lat,
                  $lng
              );

              return $coords;

          }else{
              return false;
          }

      }else{
          return false;
      }
   }
   /* Checks time stamps in the session variables to ensure that the session
    * isn't too old, and resets the UPDATE variable for the inactivity timeout
    * runs logout() if inactivity timeout or lifetime expiration are exceeded.
    */
   function validate_session() {

     if (isset($_SESSION['CREATED'])) {
       // if the session creation date stamp is older than 7 days, destroy the session
       $time_creation = time() - $_SESSION['CREATED'];
       $time_updated = time() - $_SESSION['UPDATED'];
       $time_refreshed = time() - $_SESSION['REFRESHED'];
       if (DEBUG){
          echo "Time since creation". $time_creation . "(604800)<br/>";
          echo "Time since update". $time_updated. "(7200)<br/>";
          echo "Time since refresh". $time_refreshed. "(1800)<br/>";
       }
       if ((time() - $_SESSION['CREATED'] > MAX_SESSION) || (isset($_SESSION['UPDATED']) && (time() - $_SESSION['UPDATED'] > MAX_INACTIVE ))) {
         // log out current user, if any
         logout();
         // redirect user
         redirect("/");
         }

       else {
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
     */
    function exit_with_footer(){
        require(ROOTDIR."/templates/footer.php");
        exit();
    }

    // Produces a string output to create a button in html
    //<a href="somepage.html"><button type="button">Text of Some Page</button></a>
    function button_link($link, $label)
    {
        return '<a href="'.$link.'">'
                .'<button type="button">'
                .$label
                .'</button></a>';

    }

    function live_link($link, $label)
    {
        return "<a href='$link'>$label</a>";
    }

    // Produces string output in header format.
    // Useful for synchronizing across web pages
    function form_title($label)
    {
        return '<h2>'.$label.'</h2>';
    }

    function form_subtitle($label)
    {
        return '<h3>'.$label.'</h3>';
    }

    function form_subsubtitle($label)
    {
        return '<br><b>'.$label.'</b></br>';
    }
    
    function display_image($image, $ftype, $width, $alt = " ", $title = " ") {
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

?>
