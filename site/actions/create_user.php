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
    $token_name = 'create_user_token';
    $request_token = isset($_POST[$token_name]) ? $_POST[$token_name] : "";
    if (validate_token($token_name, $request_token)) {


        // Getting the POST parameters.
        $new_id = uuid4();
        $username = htmlspecialchars($_POST['username']);
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);
        $password_again = isset($_POST['password_again']) ? $_POST['password_again'] : "";
        $today = today();
        $username_max_chars = 30;
        $email_max_chars = 50;
        $password_max_chars = 255;

        // Validating username, email and password
        $error_message = null;
        if (validate_field($username, $username_max_chars)) {
            if (validate_field($email, $email_max_chars)) {
                if (validate_field($password, $password_max_chars) && validate_field($password, $password_max_chars)) {

                    // We check if both password fields have the same value.
                    if ($password == $password_again) {

                        // Validating the password strength (regex found on https://stackoverflow.com/a/21456918)
                        $regex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/";
                        if (preg_match($regex, $password) == 1) {
                            // We check if there is already a user with that username or email like this in the database with a prepared statement.
                            $query = "SELECT * FROM users WHERE username = ? OR email = ?";
                            $stmt = $connection->prepare($query);
                            $stmt->bind_param("ss", $username, $email);
                            $stmt->execute();
                            $user = $stmt->get_result();
                            if ($user->num_rows == 0) {

                                $password_hash = password_hash($password, PASSWORD_BCRYPT);
                                $is_admin = false;
                                $picture = isset($_POST['picture']) && $_POST['picture'] != '' ? $_POST['picture'] : null;

                                // Inserting the user in the database using a prepared statement
                                $query = "INSERT INTO users VALUES(?, ?, ?, ?, ?, ?, ?)";
                                $stmt = $connection->prepare($query);
                                $stmt->bind_param("sssssss", $new_id, $username, $email, $password_hash, $picture, $is_admin, $today);
                                $was_successful = $stmt->execute();
                            } else {
                                $error_message = "There is already a user using that username or email!";
                            }
                        } else {
                            $error_message = "The password must be at least 8 characters long and contain at least one number, one lowercase letter and one uppercase letter.";
                        }
                    } else {
                        $error_message = "The passwords don't match!";
                    }
                } else {
                    $error_message = "The password is either empty or exceeds the max amount of characters (" . $email_max_chars . ")";
                }
            } else {
                $error_message = "The email is either empty or exceeds the max amount of characters (" . $email_max_chars . ")";
            }
        } else {
            $error_message = "The username is either empty or exceeds the max amount of characters (" . $username_max_chars . ")";
        }

        if (isset($error_message)) {
            redirect("/signup.php?err=" . urlencode($error_message));
        }

        if ($was_successful) {

            // We delete the token in the session
            unset($_SESSION[strtoupper($token_name)]);

            // We log in the user
            $_SESSION['CURRENT_USER_ID'] = $new_id;
            $_SESSION['CURRENT_USER_NAME'] = $username;

            redirect("/");
        } else {
            die("There was an error with the query");
        }
    }
}
