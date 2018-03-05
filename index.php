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


// Submit Recipe rout
$f3->route('GET|POST /new-recipe', function($f3) {

    $f3->set('title', 'Submit a new Recipie');
    $f3->set('content',
        'views/submit-recipe.html');





    $template = new Template();
    echo $template->render('views/base.html');
});

$f3->run();