<?php

require_once './config.php';
require_once './utils/array_validation.php';
require_once './utils/redirection.php';

// If the user is already logged in, we redirect them to the homepage.
if (isset($_SESSION['current_user_id'])) {
    redirect('/');
}
$loggedIn = false;

// Connecting to the MySQL database.
$connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($connection->connect_error) {
    die("Couldn't connect to the database");
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
        <div id="navbar"></div>
        <form class="login-container" action="">
            <div class="login-box">
                <h3>Log in</h3>
                <span class="input-group">
                    <label>Username (or e-mail)</label>
                    <input type="text" />
                </span>
                <span class="input-group">
                    <label>Password</label>
                    <input type="password" />
                </span>
                <input type="submit" />
            </div>
        </form>
    </div>

    <!-- Load navbar template -->
    <?php if ($loggedIn) { ?>
        <script>
            $.get('./templates/navbar-logged-in.html', (data) => {
                $('#navbar').replaceWith(data)
            })
        </script>
    <?php } else { ?>
        <script>
            $.get('./templates/navbar-logged-out.html', (data) => {
                $('#navbar').replaceWith(data)
            })
        </script>
    <?php } ?>

</body>

</html>