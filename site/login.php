<?php

session_start();

require_once './config.php';
require_once './utils/dbutils.php';
require_once './utils/array_validation.php';
require_once './utils/redirection.php';

// If the user is already logged in, we redirect them to the homepage.
if (isset($_SESSION['CURRENT_USER_ID'])) {
    redirect('/');
}

// Connecting to the MySQL database.
$connection = connect_to_db();

// Processing form data when form is submitted
$fields_error = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // We validate the fields
    if (trim(empty($_POST['username'])) || trim(empty($_POST['password']))) {
        $fields_error = 'Insert your username and password.';
    } else {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // We make a prepared statement to query the database since we're dealing with user input.
        $query = "SELECT id, username, password FROM users WHERE username = ? OR email = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $user = $stmt->get_result();

        // If we found a user, we validate the password
        if ($user->num_rows > 0) {
            $user_arr = $user->fetch_assoc();
            if (password_verify($password, $user_arr['password'])) {

                // We set the id of the user to the current_user_id session variable
                $_SESSION["CURRENT_USER_ID"] = $user_arr['id'];
                $_SESSION["CURRENT_USER_NAME"] = $user_arr['username'];
                $_SESSION["LAST_ACTIVITY"] = time();
                redirect("/");
            } else {
                $fields_error = "Incorrect credentials";
            }
        } else {
            $fields_error = "Incorrect credentials";
        }
    }
}

?>

<?php require_once './templates/_start_html.php' ?>
<?php $loggedIn ? require_once './templates/_navbar_logged_in.php' : require_once './templates/_navbar_logged_out.php' ?>
<form class="login-container" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
    <div class="login-box">
        <h3>Log in</h3>
        <span class="input-group">
            <label>Username (or e-mail)</label>
            <input name="username" type="text" />
        </span>
        <span class="input-group">
            <label>Password</label>
            <input name="password" type="password" />
        </span>
        <span>
            <a style="color: blue; float: right;" href="/signup.php">Don't have an account?</a>
        </span>
        <input type="submit" />
        <span class="invalid-fields"><?php echo $fields_error ?></span>
    </div>
</form>
<?php require_once './templates/_end_html.php' ?>