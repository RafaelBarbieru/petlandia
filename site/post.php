<?php

session_start();

require_once './config.php';
require_once './utils/dbutils.php';
require_once './utils/array_validation.php';
require_once './utils/date_formatting.php';

// Connecting to the MySQL database.
$connection = connect_to_db();

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
            $db_comments = $connection->query("SELECT * FROM petlandia.comments comments WHERE comments.post_id = '" . $_GET['id'] . "' ORDER BY comments.created_at DESC");
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
                            ],
                            'created_at' => $_comment['created_at']
                        ];
                    }
                }
            }
            $post = [
                'id' => $local_post['id'],
                'title' => $local_post['title'],
                'body' => $local_post['body'],
                'owner' => [
                    'name' => $owner['username'],
                    'link' => '/user.php?id=' . $owner['id'],
                    'created_at' => $owner['created_at']
                ],
                'comments' => $comments,
                'draft' => $local_post['draft'],
                'created_at' => $local_post['created_at']
            ];
        }
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
        <?php $loggedIn ? require_once './templates/_navbar_logged_in.php' : require_once './templates/_navbar_logged_out.php' ?>
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
                $post_date = $post['created_at'];

                echo "<div class='post'>";

                // We print the title
                echo "<h3>" . $post_title . "</h3>";

                // We print the owner
                if (validate_array($post_owner)) {
                    $owner_name = $post_owner['name'];
                    $owner_link = $post_owner['link'];
                    echo "<b>By: <a class='post-owner' href='" . $owner_link . "'>" . $owner_name . "</a> on " . format_date_with_time($post_date) . "</b>";
                }

                // We print the body
                echo "<p class='post-body'>" . $post_body . "</p>";

                echo "</div>";
            }
        }

        ?>

        <!-- Before displaying the comments, we check if the post is a draft once again -->
        <?php if (!$post['draft']) { ?>
            <div class="comments">
                <h3 class="comments-title">Comments (<?php echo count($post['comments']); ?>)</h3>

                <!-- In case the user is logged in, show the comment posting form. -->
                <?php if ($loggedIn) { ?>
                <form action="./actions/post_comment.php" method="POST" class="user-comment-container">                    
                    <textarea name="comment" class="comment-input" placeholder="Write a comment as <?php echo $_SESSION['CURRENT_USER_NAME'] ?>"></textarea>
                    <input type="submit" />
                    <input hidden name="user_id" value=<?php echo $_SESSION['CURRENT_USER_ID'] ?> />
                    <input hidden name="post_id" value=<?php echo $post['id'] ?> />
                    <input hidden name="url_back_reference" value="/post.php?id=<?php echo $post['id'] ?>" />
                </form>
                <?php } ?>

                <?php

                if (count($post['comments']) > 0) {

                    foreach ($post['comments'] as $comment) {
                        echo "<div class='comment'>";

                        echo "<b><a href='" . $comment['owner']['link'] . "'>" . $comment['owner']['name'] . "</a></b> (" . format_date_with_time($comment['created_at']) . ")";
                        echo "<p class='comment-body'>" . $comment['body'] . "</p>";
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

</body>

</html>