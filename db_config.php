<?php
// Database configuration settings
define('DB_SERVER', 'localhost');    // Host where your database is running
define('DB_USERNAME', 'cmsc334');    // Username for the database
define('DB_PASSWORD', 'FN(p2Sfzs;g-]Y9BH&E@c7v');  // Password for the database user
define('DB_DATABASE', "Zaowen'sTable");   // Name of the database

/**
 * Establishes a connection to the database using defined constants.
 * @return mysqli Connection object or terminates script on failure.
 */
function getDbConnection() {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
?>
