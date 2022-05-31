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
            <label for="username">Username</label>
            <input name="username" type="text" placeholder="Type your username here..." minlength="3" maxlength="30" />
        </div>

        <div>
            <label for="email">E-mail</label>
            <input name="email" type="email" placeholder="Type your email here..." maxlength="50" />
        </div>

        <div>
            <label for="password">Password</label>
            <input name="password" type="password" minlength="10" maxlength="255" />
        </div>

        <div>
            <label for="repeat_password">Repeat password</label>
            <input name="password_again" type="password" minlength="10" maxlength="255" />
        </div>

        <div>
            <label for="image">Upload profile picture (optional)</label>
            <input id="profile_pic" type="file" accept="image/jpg, image/jpeg, image/png" />
            <input id="profile_pic_data" name="picture" hidden />
        </div>

        <input type="submit" value="Sign up" />

        <!-- Field errors -->
        <span class="invalid-fields"><?php echo isset($_GET['err']) ? htmlspecialchars($_GET['err']) : "" ?></span>

        <input hidden name="create_user_token" value="<?php echo $create_user_token ?>" />
    </div>

</form>

<script>

const image =  document.getElementById('profile_pic');
image.onchange = function() {

    // Max file size is 1 MB
    if(this.files[0].size > 1048576) {
        alert("The image is too big! The maximum allowed size is 1 MB.");
        this.value = "";
    } else {

        // We convert the image to base64 and set the value of the hidden input field to that.
        const hiddenInput = document.getElementById('profile_pic_data');
        const image = this.files[0];        
        let reader = new FileReader();
        reader.onloadend = function() {
            hiddenInput.value = reader.result;
        }
        reader.readAsDataURL(image);
    }

}

</script>

<?php require_once './templates/_end_html.php' ?>