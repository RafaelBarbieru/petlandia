<?php

session_start();

require_once './config.php';
require_once './utils/dbutils.php';
require_once './utils/array_validation.php';
require_once './utils/date_formatting.php';

// Connecting to the MySQL database.
$connection = connect_to_db();

// Getting the user by their ID using a prepared statement.
$user = [];

// We escape html characters to avoid XSS attacks.
$user_id = htmlspecialchars($_GET['id']);

$query = "SELECT * FROM users WHERE id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$db_user = $stmt->get_result();

if ($db_user->num_rows > 0) {
    while ($_user = $db_user->fetch_assoc()) {
        $user = [
            'name' => $_user['username'],
            'email' => $_user['email'],
            'picture' => $_user['profile_picture'],
            'created_at' => $_user['created_at']
        ];
    }
}

// We close the connection to the database.
$connection->close();

?>

<?php require_once './templates/_start_html.php' ?>

<!-- Link jquery -->
<script src="./js/jquery.js"></script>

<!-- Body -->

<div class="user-container">
    <?php

    if (isset($user['name']) && isset($user['email'])) {
        echo "<div class='user'>";

        echo "<span>";
        echo "<h4>" . $user['name'] . "</h4>";
        echo "<small>&lt;" . $user['email'] . "&gt;</small>";
        echo "<p>Member since " . format_date($user['created_at']) . "</p>";
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

<?php require_once './templates/_end_html.php' ?>