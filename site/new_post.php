<?php

// We start the session
session_start();

?>

<?php require_once './templates/_start_html.php' ?>

<form action="post_post.php" method="POST" class="create-post-form">
    <span class="create-post-input-group">
        <label>Title</label>
        <input name="title" type="text" placeholder="Title" />
    </span>
    <span class="create-post-input-group">
        <label>Body</label>
        <textarea name="body" placeholder="Type your post here..."></textarea>
    </span>
    <span class="create-post-input-group">
        <input name="draft" type="checkbox" />Draft</input>
    </span>
    <span class="create-post-input-group">
        <input type="submit" value="Create" />
    </span>
</form>

<?php require_once './templates/_end_html.php' ?>