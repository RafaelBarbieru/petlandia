<?php

// We start the session
session_start();

require_once "./utils/token_generation.php";

// We generate a one time action token to prevent CSRF attacks.
$post_post_token = generate_token("post_post_token");

?>

<?php require_once './templates/_start_html.php' ?>

<form action="./actions/post_post.php" method="POST" class="create-post-form">

    <label>Title (max 200 characters)</label>
    <input name="title" type="text" placeholder="Type your title here..." />

    <label>Body (max 65,535 characters)</label>
    <textarea name="body" placeholder="Type your post here..."></textarea>

    <div>
        <label id="draft-label">Draft</label>
        <input id="draft-input" name="draft" type="checkbox" />
    </div>

    <input type="submit" value="Create" />

    <input hidden name="user_id" value="<?php echo htmlspecialchars($_GET['id']) ?>" />
    <input hidden name="post_post_token" value="<?php echo $post_post_token ?>" />

</form>

<?php require_once './templates/_end_html.php' ?>