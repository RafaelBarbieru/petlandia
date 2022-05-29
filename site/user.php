<?php

require './utils/array_validation.php';

// Connecting to the MySQL database.
$server = 'localhost';
$username = 'rafael';
$password = 'rafael';
$connection = new mysqli($server, $username, $password);

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

$loggedIn = true;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petlandia - User</title>

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
                    echo "<img src='./static/anonymous_user.png' />";
                }
                

                echo "</div>";
            }

            ?>
        </div>
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