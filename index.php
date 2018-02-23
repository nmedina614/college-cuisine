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
        Model::login($_POST['username'],$_POST['password']);
    }



    $template = new Template();
    echo $template->render('views/base.html');
});


// Sumbit Recipie rout
$f3->route('GET|POST /new-recipie', function($f3) {

    $f3->set('title', 'Submit a new Recipie');
    $f3->set('content',
        'views/submit-recipie.html');

    if(isset($_POST['submit'])) {
        Model::login($_POST['username'],$_POST['password']);
    }



    $template = new Template();
    echo $template->render('views/base.html');
});

$f3->run();