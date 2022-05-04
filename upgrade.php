<?php
/* Order of Precedence
 * upgrade.php
 *
 *
 * Incrementally upgrades the database, no matter how many versions behind it
 * is.
 *
 * When adding a new version, place it at the tail end!
 */
require("includes/config.php");
// TODO add some nice styling and admin-facing info

// Make sure we arrived here via a submit button
if (isset($_GET['upgrade'])) {
  if ($_GET['upgrade'] !== 'True') {
    die(bs_alert('Something went wrong with the submit button', 'danger'));
  }

  $version = "0.0";

  // establish database connection
  $cxn = open_db_browse();

  // check for presence of Appdata table. 
  $query = "SHOW TABLES LIKE 'Appdata'";
  try {
  $sth = $cxn->prepare($query);  
  $sth->execute();
  } catch (PDOException $e) {
    if (DEBUG) {
      $message = $e->getMessage();
      $code = (int)$e->getCode();
      $error = "Problem encountered querying for presence of Appdata table. $message / $code";
    } else {
      $error = "I couldn't check to see if there's already an Appdata table.";
    }
    die(bs_alert($error, 'danger'));
  }
  // assuming query executed properly, find out answer to our question
  if ($sth->rowCount() == 1) {
    // query returned 1 row (our table), so let's ask for the current app version
    // we should only ever have one row in Appdata, so we always know that app_id=1
    $query = "SELECT db_version FROM Appdata WHERE app_id=1";
    $sth = $cxn->prepare($query);
    try {
    $sth->execute();
    } catch (PDOException $e) {
      if (DEBUG) {
        $message = $e->getMessage();
        $code = (int)$e->getCode();
        $error = "Issue fetching current db version. $message / $code";
      } else {
        $error = "I couldn't run a query against the database.";
      } 
      die(bs_alert($error, 'danger'));
    }
    $result = $sth->fetch(PDO::FETCH_ASSOC);
    $version = $result['db_version'];
  } else {
    // table not found in db, assume version is 0.1
    $version = "0.1";
    if (DEBUG) {
      bs_alert("did not find Appdata table, assuming v0.1", 'info');
    }
  } // end of step to find current app version
  
  // Version 0.1 to 1.0 upgrade section
  if ($version == "0.1") {
    bs_alert("Updating from 0.1 to 1.0<br>", 'info');

    // (0.1->1.0) Job 1: Password Security Update
    try {
    $query = "ALTER TABLE WebUsers "
            . "ADD COLUMN `hash_webuser` CHAR(255) DEFAULT NULL, "
            . "ADD COLUMN `last_logged_in` TIMESTAMP NULL;";
    $sth = $cxn->prepare($query);
    $sth->execute();
            
    } catch (PDOException $e) {
      if (DEBUG) {
        $message = $e->getmessage();
        $code = (int)$e->getCode();
        $error = "Error updating WebUsers Table. $message / $code";
      } else {
        $error = "Could not update WebUsers Table.";
      }
      die (bs_alert($error, 'danger'));
    } // end of Job 1 try/catch block
    bs_alert("Successfully updated WebUsers Table.", 'success');
    
    // (0.1->1.0) Job 2: add invitations table
    try {
      $query = "CREATE TABLE Invites ( "
                . "invite_id SMALLINT NOT NULL AUTO_INCREMENT, "
                . "invite_email VARCHAR(255) NOT NULL, "
                . "invite_key VARCHAR(255) NOT NULL, "
                . "invite_expires DATETIME NOT NULL, "
                . "invite_used BOOLEAN NOT NULL DEFAULT 0, "
                . "PRIMARY KEY (invite_id)"
                . " )";
      $sth = $cxn->prepare($query);
      $sth->execute();
      } catch (PDOException $e) {
          if (DEBUG) {
            $message = $e->getMessage();
            $code = (int)$e->getCode();
            $error = "Error adding invites table. $message / $code";
          } else {
            $error = "Could not add invites table.";
          }
          die(bs_alert($error, 'danger'));
      } // end of Job 2 try/catch block
    bs_alert("Successfully added the invites table.", "success");

    // (0.1->1.0) Job 3: add database info table
    try {
      $query = "CREATE TABLE Appdata ( "
              . "app_id SMALLINT NOT NULL AUTO_INCREMENT, "
              . "app_version VARCHAR(10) NOT NULL, "
              . "host_kingdom_name VARCHAR(50) NOT NULL, "
              . "host_kingdom_id SMALLINT NOT NULL, "
              . "PRIMARY KEY (app_id) "
              . ")";
      $sth = $cxn->prepare($query);
      $sth->execute();
    } catch (PDOException $e) {
      if (DEBUG) {
        $message = $e->getMessage();
        $code = (int)$e->getCode();
        $error = "Error adding Appdata table. $message / $code";
      } else {
        $error = "Could not add Appdata table.";
      }
      die(bs_alert($error, 'danger'));
    } // end of Job 3 try/catch block
    bs_alert("Successfully added the Appdata table.", 'success');
    
    // (0.1->1.0) Job 4: populate new database info table
    try {
      // keys: app_id, app_version, host_kingdom_name, host_kingdom_id
      // if we're upgrading from v 0.0 to v1.0, k-name and k-id are stored
      // in constants.php
      $query = "INSERT INTO Appdata VALUES (NULL, :version, :k_name, :k_id)";
      $version = "1.0";
      $k_name = HOST_KINGDOM;
      $k_id = HOST_KINGDOM_ID;
      $data = [':version' => $version, ':k_name' => $k_name, ':k_id' => $k_id];
      $sth = $cxn->prepare($query);
      $result = $sth->execute($data);

/* not sure we need this, keeping it in case we do -Cordelya 2022-05-04
      // if query returned false but didn't already, throw an exception
      if ($result != True) {
        if (DEBUG) {
          $message = $sth->errorCode();
          throw new Exception("Failed to init Appdata table. No rows returned. $message");
        } else {
        throw new Exception("Failed to initialize Appdata table with info row ";
                          . "based on existing constants.");
        }
      }
 */

    } catch (PDOException $e) {
      if (DEBUG) {
        $message = $e->getMessage();
        $code = (int)$e->getCode();
        $error = "Failed to execute query to init Appdata table row. $message / $data";
      } else {
        $error = "Failed to execute query to init Appdata table row.";
      }
      die(bs_alert($error, 'danger'));
    } catch (Exception $e) {
        $error = $e->getMessage();
        die(bs_alert($error, 'danger'));
    } // end of Job 4 try/catch block
    bs_alert("Successfully added the Appdata table", 'success');  
  } //end of v0.1 to v1.0 upgrade section
  bs_alert("Successfully upgraded from v0.1 to v1.0", 'success');

  // begin next upgrade block here
  
  // end of upgrades. Let's send a happy report!
  echo "<p>Database upgrades complete! You may now go <a href='/'>home</a>.</p>";

  $cxn = null; // close the db connection

} // end of "if $_POST['submit'] is set"
else {
  // we did not come here by submit and need to generate the form.
  // TODO basic bootstrap page layout & styling

  echo "<html><head><title>Upgrade the Database</title></head>";
  echo "<body>";
  echo "<h1>Upgrade the database?</h1>";
  echo "<form action='upgrade.php'>";
  echo "<input type='hidden' id='upgrade' name='upgrade' value='True'>";
  echo "<input type='submit' value='Upgrade!'>";
  echo "</form></body></html>";


}

?>
