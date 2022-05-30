<?php

session_start();

require_once './config.php';
require_once './utils/dbutils.php';
require_once './utils/array_validation.php';
require_once './utils/date_formatting.php';

// Connecting to the MySQL database.
$connection = connect_to_db();

// Filling the posts array with data from the database with a raw query because there are no parameters.
$posts = [];
$db_posts = $connection->query("SELECT * FROM petlandia.posts");

if ($db_posts->num_rows > 0) {
    while ($post = $db_posts->fetch_assoc()) {

        // We check the visibility of the post (whether it's a draft or not) and decide to show it or not.
        if (!$post['draft']) {

            // We get the post's owner information from the database using a prepared statement.
            $query = "SELECT * FROM petlandia.users users WHERE users.id = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("s", $post['user_id']);
            $stmt->execute();
            $db_post_owner = $stmt->get_result();
            if ($db_post_owner->num_rows > 0) {

                $post_owner = $db_post_owner->fetch_assoc();

                // We append the information to the $posts array we'll later use to show in the HTML code.
                $posts[] = [
                    'id' => $post['id'],
                    'title' => $post['title'],
                    'body' => $post['body'],
                    'owner' => [
                        'name' => $post_owner['username'],
                        'link' => '/user.php?id=' . $post_owner['id'],
                        'created_at' => $post_owner['created_at']
                    ],
                    'link' => "/post.php?id=" . $post['id'],
                    'created_at' => $post['created_at']
                ];
            }
        }
    }
}

?>

<?php require_once './templates/_start_html.php' ?>

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
            $post_date = $post['created_at'];


            // We print the title
            echo "<h3><a href='" . $post_link . "'>" . $post_title . "</a></h3>";

            // We print the owner
            if (validate_array($post_owner)) {
                $owner_name = $post_owner['name'];
                $owner_link = $post_owner['link'];
                echo "<b>By: <a class='post-owner' href='" . $owner_link . "'>" . $owner_name . "</a> on " . format_date_with_time($post_date) . "</b>";
            }

            // If the post's body exceeds 300 characters, we only show the first 297 and an ellipsis at the end.
            if (strlen($post_body) > 300) {
                echo "<p class='post-body'>" . substr($post_body, 0, 297) . "...</p>";
            } else {
                echo "<p class='post-body'>" . $post_body . "</p>";
            }

            // We print the link
            echo "<a class='go-to-post' href='" . $post_link . "'>Go to post</a>";

            // We print the number of comments after getting them from the database using a prepared statement
            $query = "SELECT count(*) FROM petlandia.comments WHERE post_id = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("s", $post['id']);
            $stmt->execute();
            $number_of_comments = $stmt->get_result();
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

<?php require_once './templates/_end_html.php' ?>