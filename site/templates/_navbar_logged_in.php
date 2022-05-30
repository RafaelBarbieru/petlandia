<nav class="navbar">
    <span>
        <h1>Petlandia</h1>
        <span>(Logged in as <?php echo $_SESSION['CURRENT_USER_NAME'] ?>)</span>
    </span>
    <ul>
        <li><a href="/">Home</a></li>
        <li><a href="/user.php?id=<?php echo $_SESSION['CURRENT_USER_ID'] ?>">Profile</a></li>
        <li><a href="/new_post.php?id=<?php echo $_SESSION['CURRENT_USER_ID'] ?>"">Create a post</a></li>
        <li><a href="/logout.php">Log out</a></li>
    </ul>
</nav>