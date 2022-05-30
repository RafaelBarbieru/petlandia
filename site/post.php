<?php

session_start();

require_once './config.php';
require_once './utils/dbutils.php';
require_once './utils/array_validation.php';
require_once './utils/date_formatting.php';
require_once './utils/token_generation.php';

// Connecting to the MySQL database.
$connection = connect_to_db();

// Getting the post by its ID using a prepared statement.
$post = [];
$post_id = htmlspecialchars($_GET['id']);
$query = "SELECT * FROM posts WHERE id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $post_id);
$stmt->execute();
$db_post = $stmt->get_result();

if ($db_post->num_rows > 0) {
    while ($local_post = $db_post->fetch_assoc()) {

        // Getting the post owner with a raw query, since the user_id comes directly from the database, so the user can't temper with it.
        $db_owner = $connection->query("SELECT * FROM petlandia.users users WHERE users.id = '" . $local_post['user_id'] . "'");
        if (isset($db_owner) && $db_owner->num_rows > 0) {

            $owner = $db_owner->fetch_assoc();

            // Getting the post's comments using a prepared statement.
            $query = "SELECT * FROM comments WHERE post_id = ? ORDER BY created_at DESC";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("s", $post_id);
            $stmt->execute();
            $db_comments = $stmt->get_result();
            $comments = [];
            if ($db_comments->num_rows > 0) {
                while ($_comment = $db_comments->fetch_assoc()) {

                    // Getting the comment's owner with a raw query because it comments directly from the database and the user can't temper with it.
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

?>

<?php require_once './templates/_start_html.php' ?>
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
<?php if (isset($post['draft']) && !$post['draft']) { ?>
    <div class="comments">
        <h3 class="comments-title">Comments (<?php echo isset($post['comments']) ? count($post['comments']) : 0; ?>)</h3>

        <!-- In case the user is logged in, show the comment posting form. -->
        <?php if ($loggedIn) {

            // We generate a one time action token to prevent CSRF attacks.
            $post_comment_token = generate_token("post_comment_token");

        ?>

            <form action="./actions/post_comment.php" method="POST" class="user-comment-container">
                <textarea name="comment" class="comment-input" placeholder="Write a comment as <?php echo $_SESSION['CURRENT_USER_NAME'] ?>"></textarea>
                <input type="submit" />
                <input hidden name="user_id" value=<?php echo $_SESSION['CURRENT_USER_ID'] ?> />
                <input hidden name="post_id" value=<?php echo $post['id'] ?> />
                <input hidden name="url_back_reference" value="/post.php?id=<?php echo $post['id'] ?>" />
                <input hidden name="post_comment_token" value="<?php echo $post_comment_token ?>" />
            </form>
        <?php } else { ?>
            <p>You need to <a style="text-decoration: underline; color: blue;" href="/login.php">Log in</a> in order to comment.</p>
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
<?php require_once './templates/_end_html.php' ?>