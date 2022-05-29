<?php

session_start();

require_once './config.php';
require_once './utils/array_validation.php';

// Connecting to the MySQL database.
$connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($connection->connect_error) {
    die("Couldn't connect to the database");
}

// Getting the post by its ID.
$post = [];
$db_post = $connection->query("SELECT * FROM petlandia.posts posts WHERE posts.id = '" . $_GET['id'] . "'");

if ($db_post->num_rows > 0) {
    while ($local_post = $db_post->fetch_assoc()) {

        // Getting the post owner.
        $db_owner = $connection->query("SELECT * FROM petlandia.users users WHERE users.id = '" . $local_post['user_id'] . "'");
        if ($db_owner->num_rows > 0) {

            $owner = $db_owner->fetch_assoc();

            // Getting the post's comments
            $db_comments = $connection->query("SELECT * FROM petlandia.comments comments WHERE comments.post_id = '" . $_GET['id'] . "'");
            $comments = [];
            if ($db_comments->num_rows > 0) {
                while ($_comment = $db_comments->fetch_assoc()) {

                    // Getting the comment's owner
                    $comment_db_owner = $connection->query("SELECT * FROM petlandia.users users WHERE users.id = '" . $_comment['user_id'] . "'");
                    if ($comment_db_owner->num_rows > 0) {
                        $comment_owner = $comment_db_owner->fetch_assoc();
                        $comments[] = [
                            'body' => $_comment['body'],
                            'owner' => [
                                'name' => $comment_owner['username'],
                                'link' => '/user.php?id=' . $comment_owner['id']
                            ]
                        ];
                    }
                }
            }
            $post = [
                'title' => $local_post['title'],
                'body' => $local_post['body'],
                'owner' => [
                    'name' => $owner['username'],
                    'link' => '/user.php?id=' . $owner['id']
                ],
                'comments' => $comments,
                'draft' => $local_post['draft']
            ];
        }
    }
}

// We close the connection to the database.
$connection->close();

if (isset($_SESSION['current_user_id'])) {
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
    <title>Petlandia - <?php echo (isset($post['title']) ? $post['title'] : '') ?></title>

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
        <?php

        // Making sure all the require_onced post's fields are set.
        if (isset($post['title'])) {

            // We check if the post is a draft
            if ($post['draft']) {
                echo "This post is a draft and you're not authorized to view it.";
            } else {
                // Saving the post's fields in variables for easier access.
                $post_title = $post['title'];
                $post_body = $post['body'];
                $post_owner = $post['owner'];

                echo "<div class='post'>";

                // We print the title
                echo "<h3>" . $post_title . "</h3>";

                // We print the owner
                if (validate_array($post_owner)) {
                    $owner_name = $post_owner['name'];
                    $owner_link = $post_owner['link'];
                    echo "<b>By: <a href='" . $owner_link . "'>" . $owner_name . "</a></b>";
                }

                // We print the body
                echo "<p>" . $post_body . "</p>";

                echo "</div>";
            }
        }

        ?>

        <!-- Before displaying the comments, we check if the post is a draft once again -->
        <?php if (!$post['draft']) { ?>
            <div class="comments">
                <h3 class="comments-title">Comments (<?php echo count($post['comments']); ?>)</h3>

                <?php

                if (count($post['comments']) > 0) {

                    foreach ($post['comments'] as $comment) {
                        echo "<div class='comment'>";

                        echo "<b><a href='" . $comment['owner']['link'] . "'>" . $comment['owner']['name'] . "</a></b>";
                        echo "<p>" . $comment['body'] . "</p>";
                        echo "<hr /";

                        echo "</div>";
                    }
                } else {
                    echo "<p>There are no comments on this post</p>";
                }

                ?>

            </div>
        <?php } ?>
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