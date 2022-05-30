<?php

// We start the session
session_start();

require_once "./utils/token_generation.php";

// We generate a one time action token to prevent CSRF attacks.
$create_user_token = generate_token("create_user_token");

?>

<?php require_once './templates/_start_html.php' ?>

<form action="./actions/create_user.php" method="POST" class="create-user-form">

    <h3>Sign up</h3>

    <div>
        <div>
            <label>Username</label>
            <input name="username" type="text" placeholder="Type your username here..." minlength="3" maxlength="30" />
        </div>

        <div>
            <label>E-mail</label>
            <input name="email" type="email" placeholder="Type your email here..." maxlength="50" />
        </div>

        <div>
            <label>Password</label>
            <input name="password" type="password" maxlength="255" />
        </div>

        <div>
            <label>Repeat password</label>
            <input name="password_again" type="password" maxlength="255" />
        </div>

        <input type="submit" value="Sign up" />

        <!-- Field errors -->
        <span class="invalid-fields"><?php echo isset($_GET['err']) ? htmlspecialchars($_GET['err']) : "" ?></span>

        <input hidden name="create_user_token" value="<?php echo $create_user_token ?>" />
    </div>

</form>

<?php require_once './templates/_end_html.php' ?>