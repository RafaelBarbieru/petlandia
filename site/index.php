<?php

session_start();

require_once './config.php';
require_once './utils/array_validation.php';

// Connecting to the MySQL database.
$connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($connection->connect_error) {
    die("Couldn't connect to the database");
}

// Filling the posts array with data from the database.
$posts = [];
$db_posts = $connection->query("SELECT * FROM petlandia.posts");

if ($db_posts->num_rows > 0) {
    while ($post = $db_posts->fetch_assoc()) {

        // We check the visibility of the post (whether it's a draft or not) and decide to show it or not.
        if (!$post['draft']) {

            // We get the post's owner information from the database.
            $db_post_owner = $connection->query("SELECT * FROM petlandia.users users WHERE users.id = '" . $post['user_id'] . "'");
            if ($db_post_owner->num_rows > 0) {

                $post_owner = $db_post_owner->fetch_assoc();

                // We append the information to the $posts array we'll later use to show in the HTML code.
                $posts[] = [
                    'id' => $post['id'],
                    'title' => $post['title'],
                    'body' => $post['body'],
                    'owner' => [
                        'name' => $post_owner['username'],
                        'link' => '/user.php?id=' . $post_owner['id']
                    ],
                    'link' => "/post.php?id=" . $post['id']
                ];
            }
        }
    }
}

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
    <title>Petlandia - Home</title>

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
        <div class="posts">
            <?php foreach ($posts as $post) {

                // Making sure all the post's fields are set.
                if (validate_array($post)) {

                    echo "<div class='list-post'>";

                    // Saving the post's fields in variables for easier access.
                    $post_title = $post['title'];
                    $post_body = $post['body'];
                    $post_owner = $post['owner'];
                    $post_link = $post['link'];


                    // We print the title
                    echo "<h3><a href='" . $post_link . "'>" . $post_title . "</h3>";

                    // We print the owner
                    if (validate_array($post_owner)) {
                        $owner_name = $post_owner['name'];
                        $owner_link = $post_owner['link'];
                        echo "<b>By: <a href='" . $owner_link . "'>" . $owner_name . "</a></b>";
                    }

                    // If the post's body exceeds 300 characters, we only show the first 297 and an ellipsis at the end.
                    if (strlen($post_body) > 300) {
                        echo "<p>" . substr($post_body, 0, 297) . "...</p>";
                    } else {
                        echo "<p>" . $post_body . "</p>";
                    }

                    // We print the link
                    echo "<a class='go-to-post' href='" . $post_link . "'>Go to post</a>";

                    // We print the number of comments after getting them from the database
                    $number_of_comments = $connection->query("SELECT count(*) FROM petlandia.comments WHERE post_id = '" . $post['id'] . "'");
                    if ($number_of_comments->num_rows > 0) {
                        echo "<span style='margin-left: 1rem;'>Comments: " . $number_of_comments->fetch_array()[0] . "</span>";
                    }

                    echo "</div>";
                }
            }

            // We close the database connection because we don't need it anymore.
            $connection->close();

            ?>
        </div>
    </div>
</body>

</html>