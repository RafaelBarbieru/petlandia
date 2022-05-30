<?php

function connect_to_db()
{
    $connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($connection->connect_error) {
        die("Couldn't connect to the database");
    }

    return $connection;
}
