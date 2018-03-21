<!DOCTYPE html>

<html lang="en">
<head>

    <!--Image for Title -->
    <link rel="shortcut icon" href="<?= ($BASE) ?>/assets/images/icons/logo.ico" />

    <title><?= ($title) ?></title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">

    <!-- Page Styles -->
<?php foreach (($styles?:[]) as $stylepath): ?>
    <link rel="stylesheet" type="text/css" href="<?= ($stylepath) ?>">
<?php endforeach; ?>

</head>
<body>

<?php foreach (($includes?:[]) as $path): ?>
<?php echo $this->render($path,NULL,get_defined_vars(),0); ?>

<?php endforeach; ?>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>


<?php foreach (($scripts?:[]) as $scriptpath): ?>
<script type="text/javascript" src="<?= ($scriptpath) ?>"></script>
<?php endforeach; ?>

</body>
</html>
