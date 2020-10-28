<?php
/**
 * Connect to a database.
 */
try {
    $dbh = new PDO(
        "mysql:host=localhost;dbname=000792459",
        "root",
        ""
    );
} catch (Exception $e) {
    die("ERROR: Couldn't connect. {$e->getMessage()}");
}