<?php
/**
 * User: Aaron Melhaff
*/

//Begin session
session_start();

// Require f3
require_once('vendor/autoload.php');

// Setup
$f3 = Base::instance();

// Establish database connection.
Model::connect();

// Homepage route.
$f3->route('GET /', function($f3) {

    $f3->set('title', 'College Cuisine');
    $f3->set('content', 'views/home.html');



    $template = new Template();
    echo $template->render('views/base.html');
});

// Login route.
$f3->route('GET|POST /login', function($f3) {

    $f3->set('title', 'Login');
    $f3->set('content',
        'views/login.html');

    if(isset($_POST['submit'])) {

        $result = Model::login();

        // If login is successful, redirect to main page.
        if($result != false) {

            $f3->reroute('/');

        } else { // Otherwise generate error.

            $f3->set('invalid', true);
        }

        // Make username sticky.
        if(isset($_POST['username'])) {

            $f3->set('username', $_POST['username']);

        }
    }

    session_unset();

    $template = new Template();
    echo $template->render('views/base.html');
});


// Sumbit Recipe route
$f3->route('GET|POST /recipe/@recipeID', function($f3, $params) {

    //SQL to get Recipe Name
    $f3->set('title', $params['recipeID']);
    $f3->set('content',
        'views/recipe.html');
    $f3->set('recipeID', $params['recipeID']);
    //print_r($_POST);

    if(isset($_POST['submit'])) {
        Model::login($_POST['username'],$_POST['password']);
    }



    $template = new Template();
    echo $template->render('views/base.html');
});

$f3->route('GET /profiles/@user', function($f3, $params) {
    //SQL to get Recipe Name
    $f3->set('title', $params['user']);
    $f3->set('content',
        'views/profile.html');

    if(isset($_POST['submit'])) {
        Model::login($_POST['username'],$_POST['password']);
    }



    $template = new Template();
    echo $template->render('views/base.html');
});


$f3->run();