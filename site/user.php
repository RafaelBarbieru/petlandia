<?php

session_start();

require_once './config.php';
require_once './utils/array_validation.php';

// Connecting to the MySQL database.
$connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($connection->connect_error) {
    die("Couldn't connect to the database");
}

// Getting the user by their ID.
$user = [];
$db_user = $connection->query("SELECT * FROM petlandia.users users WHERE users.id = '" . $_GET['id'] . "'");

if ($db_user->num_rows > 0) {
    while ($_user = $db_user->fetch_assoc()) {
        $user = [
            'name' => $_user['username'],
            'email' => $_user['email'],
            'picture' => $_user['profile_picture']
        ];
    }
}

// We close the connection to the database.
$connection->close();

if (isset($_SESSION['CURRENT_USER_ID'])) {
    $loggedIn = true;
} else {
    $loggedIn = false;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petlandia - <?php echo isset($user['name']) ? $user['name'] : "Anonymous user" ?></title>

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
        <div class="user-container">
            <?php

            if (isset($user['name']) && isset($user['email'])) {
                echo "<div class='user'>";

                echo "<span>";
                echo "<h4>" . $user['name'] . "</h4>";
                echo "<small>&lt;" . $user['email'] . "&gt;</small>";
                echo "</span>";

                // We set the default profile picture if the user doesn't have one assigned
                if (isset($user['picture'])) {
                    echo "<img src='" . $user['picture'] . "' />";
                } else {
                    echo "<img src='./images/anonymous_user.png' />";
                }


                echo "</div>";
            }

            ?>
        </div>
    </div>

</body>

</html>