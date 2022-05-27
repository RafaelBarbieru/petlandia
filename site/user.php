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

// Getting the post by its ID.
$post = [];

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
            <div class="user">
                <span>
                    <h4>sprotgbMXwZaHr36Re2a!g6JJLLfdS </h4>
                    <small>&lt;baracri00@gmail.com&gt;</small>
                </span>
                <img src="https://media-exp1.licdn.com/dms/image/C5603AQHohPMryw7uSQ/profile-displayphoto-shrink_400_400/0/1653223370843?e=1658966400&v=beta&t=h1w7FFhdjZ_6s5L-Euo-90PxwZsQxR9LTgj6q7PkXco" />
            </div>

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