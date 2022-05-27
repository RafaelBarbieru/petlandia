<?php
$loggedIn = true;
$post = [
    'title' => 'Hello World',
    'body' => 'Hello my world, how are you?',
    'owner' => [
        'name' => 'Administrator',
        'link' => 'https://google.com'
    ],
    'link' => 'https://google.com'
]
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petlandia | <?php echo(isset($post['title']) ? $post['title'] : '') ?></title>

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