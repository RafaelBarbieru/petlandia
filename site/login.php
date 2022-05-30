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
$loggedIn = false;

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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petlandia - Login</title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>

    <!-- Link jquery -->
    <script src="./js/jquery.js"></script>

    <!-- Body -->
    <div class="container">
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
                <input type="submit" />
                <span class="invalid-fields"><?php echo $fields_error ?></span>
            </div>
        </form>
    </div>

</body>

</html>