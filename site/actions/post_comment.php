<?php

session_start();

require_once '../config.php';
require_once '../utils/dbutils.php';
require_once '../utils/uuid.php';
require_once '../utils/redirection.php';
require_once '../utils/date_formatting.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Connecting to the MySQL database.
    $connection = connect_to_db();

    // We validate the anti-CSRF token
    $request_token = $_POST['post_comment_token'];
    if (isset($request_token) && isset($_SESSION['POST_COMMENT_TOKEN']) && $request_token == $_SESSION['POST_COMMENT_TOKEN']) {
        // Getting the POST parameters.
        $new_id = uuid4();
        $user_id = $_POST['user_id'];
        $post_id = $_POST['post_id'];
        $body = $_POST['comment'];
        $today = today();
        $url_back_reference = $_POST['url_back_reference'];

        // Validating body length
        if (isset($body) && strlen($body) > 0 && strlen($body) <= 2000) {
            // Making a prepared statement
            $query = "INSERT INTO comments VALUES(?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("sssss", $new_id, $user_id, $post_id, $body, $today);
            $was_successful = $stmt->execute();
        } else {
            $connection->close();
            die("Your comment is invalid or exceeds the maximum amount of characters (2000)");
        }
    } else {
        $connection->close();
        die("This request was detected as suspicious of a CSRF attack. Please, try again.");
    }

    // Closing the db connection.
    $connection->close();

    if ($was_successful) {

        // We delete the token in the session
        unset($_SESSION['POST_COMMENT_TOKEN']);

        redirect($url_back_reference);
    } else {
        die("There was an error with the query");
    }
}
