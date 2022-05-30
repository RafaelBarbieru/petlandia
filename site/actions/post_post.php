<?php

session_start();

require_once '../config.php';
require_once '../utils/dbutils.php';
require_once '../utils/uuid.php';
require_once '../utils/redirection.php';
require_once '../utils/date_formatting.php';
require_once '../utils/field_validation.php';
require_once '../utils/token_generation.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Connecting to the MySQL database.
    $connection = connect_to_db();

    // We validate the anti-CSRF token
    $token_name = 'post_post_token';
    $request_token = isset($_POST[$token_name]) ? $_POST[$token_name] : "";
    if (validate_token($token_name, $request_token)) {

        // Getting the POST parameters.
        $new_id = uuid4();
        $user_id = htmlspecialchars($_POST['user_id']);
        $title = htmlspecialchars($_POST['title']);
        $body = htmlspecialchars($_POST['body']);
        $draft = isset($_POST['draft']) ? true : false;
        $today = today();
        $title_max_chars = 200;
        $body_max_chars = 65535;

        // Validating title and body length
        if (validate_field($title, $title_max_chars)) {
            if (validate_field($body, $body_max_chars)) {

                // Making a prepared statement
                $query = "INSERT INTO posts VALUES(?, ?, ?, ?, ?, ?)";
                $stmt = $connection->prepare($query);
                $stmt->bind_param("ssssss", $new_id, $title, $body, $draft, $user_id, $today);
                $was_successful = $stmt->execute();

            } else {
                $connection->close();
                die("Your post body is invalid or exceeds the maximum amount of characters (" . $body_max_chars . ")");
            }
        } else {
            $connection->close();
            die("Your post title is invalid or exceeds the maximum amount of characters (" . $title_max_chars . ")");
        }
    } else {
        $connection->close();
        die("This request was detected as suspicious of a CSRF attack. Please, try again.");
    }

    // Closing the db connection.
    $connection->close();

    if ($was_successful) {

        // We delete the token in the session
        unset($_SESSION[strtoupper($token_name)]);

        redirect("/");
    } else {
        die("There was an error with the query");
    }
}
