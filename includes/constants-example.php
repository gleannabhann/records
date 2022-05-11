<?php

    /**
     * constants-example.php
     *
     * copy this file to constants.php
     *
     * Global constants.
     */

    // Database information for the Hall of Records
    define("DATABASE", "database");
    define("PASSWORD", "password");
    define("SERVER", "localhost");
    define("USERNAME", "db_username");

    // Information for the host kingdom
    // deprecated in v1.0, will be removed in subsequent version
    define("HOST_KINGDOM", "");
    define("HOST_KINGDOM_ID", "");

    // Information for password security
    // Populate with at least 32 random characters, excluding spaces
    // deprecated in v1.0, will be removed in subsequent version
    define("SALT", "");

    // Invitation key salt - populate with 10-15 random chars
    define("KEYSALT", "");

    // Invite key expires after days
    // must be expressed using DateInterval formatting
    define("INV_EXP", "P7D");

    // Information for website structure
    // this is the system path to the top level directory
    // where index.php and README.md reside
    define("ROOTDIR", "/path/to/index/file/");

    // where to send custom "debug" level messages
    // will be deprecated in v.2.0 in favor of a logging framework plugin
    // in order for this to work as set up here, the server
    // user that is responsible for the web process (ie "www-data")
    // must be able to write to the directory /var/log/php/ so that it
    // can create the log file if one doesn't exist.
    // Don't expose debug log files to /var/www/ if you can help it.
    define("DEBUG", "1"); // 0 = off, 1 = on
    define("DEBUG_DEST", "/var/log/php/debug.log");

    /* Session expiration settings *
     * MAX_SESSION is the absolute maximum length a session should be
     * allowed to continue.
     *
     * MAX_INACTIVE is the length of time after which the session is
     * destroyed if no activity occurs.
     *
     * SESSION_REFRESH is the interval at which an active session is
     * refreshed.
     *
     * each setting should be expressed in seconds.
     */
     // max time a session should be active, in seconds
    define("MAX_SESSION", "604800"); //604800 = 7 days

    // max amount of inacivity before session is expired, in seconds
    define("MAX_INACTIVE", "7200"); // 7200 = 2 hours
    define("SESSION_REFRESH", "1800"); // 1800 = 30 minutes
