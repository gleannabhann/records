<?php

    /**
     * constants-example.php
     *
     * Global Constants
     * Copy this file to constants.php and fill in the details where necessary.
     */

    /** Database server name */
    define("SERVER", "localhost");

    /** Database name */
    define("DATABASE", "database");

    /** Database user's username */
    define("USERNAME", "db_username");

    /** Database user password */
    define("PASSWORD", "password");

    /**
     * Information for password security
     *
     * Populate with at least 32 random characters, excluding spaces
     * @deprecated in v1.0, will be removed in subsequent version
     */
    define("SALT", "");

    /**
     * Invitation key salt.
     *
     * Populate with 10-20 random chars
     */
    define("KEYSALT", "");

    /**
     * Invitation key expiration delay
     *
     * For how long should an invitation key be valid?
     * Must be expressed using DateInterval formatting
     */
    define("INV_EXP", "P7D");

    /**
     * Information for website structure
     *
     * this is the system path to the top level directory
     * where index.php and README.md reside
     */
    define("ROOTDIR", "/path/to/index/file/");

    /**
     * Google Maps API Key
     *
     * In order to load the Event Site(s) Map, a Google Maps API key is
     * required. You should limit the scope of the key to calls originating
     * from your app's domain because this key will be discoverable via
     * examination of page source.
     *
     * example:
     * <code>
     * <?php
     * define("MAPSAPI", "paste-your-api-key-here");
     * ?>
     * </code>
     */
    define("MAPSAPI", "");

    /**
     * Google Analytics tracking code
     * Place your Google Analytics tracking code here and it will be
     * inserted into the GA javascript widget in the footer
     *
     * example
     * <code>
     * <?php
     * define("ANALYTICS", "your-code-here");
     * ?>
     * </code>
     */
    define("ANALYTICS", "");

    /**
     * Toggles debugging output
     *
     * Set to 1 for debugging output
     * Set to 0 for no debugging output
     * Will be deprecated in the future
     */
    define("DEBUG", "1");

    /**
     * Debug log location
     *
     * where to send custom "debug" level messages will be deprecated in v.2.0
     * in favor of a logging framework plugin in order for this to work as set
     * up here, the server user that is responsible for the web process
     * (ie "www-data") must be able to write to the directory /var/log/php/ so
     * that it can create the log file if one doesn't exist. Don't expose debug
     * log files to /var/www/ if you can help it.
     */
    define("DEBUG_DEST", "/var/log/php/debug.log");

    /**
     * Session Maximum Length
     *
     * MAX_SESSION is the absolute maximum length of time, in seconds, a session
     * should be allowed to continue.
     */
    define("MAX_SESSION", "604800"); //604800 = 7 days

    /**
     * Session Maximum Inactivity
     *
     * MAX_INACTIVE is the length of time, in seconds, after which the session is
     * destroyed if no activity occurs.
     */
   define("MAX_INACTIVE", "7200"); // 7200 = 2 hours

    /**
     * Session refresh interval
     *
     * SESSION_REFRESH is the interval of time, in seconds, at which an active
     * session is refreshed.
     */
    define("SESSION_REFRESH", "1800"); // 1800 = 30 minutes

    /* If you need to add more constants, add them above this line */

    /** Host kingdom name
     *
     * install.php will add these at the end of the install process if they are
     * not already defined. TODO make the install script use them if they are
     * defined and append them if they are not.
     *
     * if you don't use the install script, uncomment these and fill them in
     * after you add the host kingdom to the database */
    //define("HOST_KINGDOM", "");
    //define("HOST_KINGDOM_ID", "");
