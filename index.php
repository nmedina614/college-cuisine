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

    // Title to use in template.
    $title = "College Cuisine";

    // List of paths to stylesheets.
    $styles = array();

    // List of paths for sub-templates being used.
    $includes = array(
        'views/_nav.html',
        'views/_home.html'
    );

    // List of paths to scripts being used.
    $scripts = array();

    $f3->set('title',    $title);
    $f3->set('styles',   $styles);
    $f3->set('includes', $includes);
    $f3->set('scripts',  $scripts);

    // Display Template
    $template = new Template();
    echo $template->render('views/_base.html');
});

// Login route.
$f3->route('GET|POST /login', function($f3) {

    // Title to use in template.
    $title = "Login";

    // List of paths to stylesheets.
    $styles = array();

    // List of paths for sub-templates being used.
    $includes = array(
        'views/_nav.html',
        'views/_login.html'
    );

    // List of paths to scripts being used.
    $scripts = array();

    $f3->set('title',    $title);
    $f3->set('styles',   $styles);
    $f3->set('includes', $includes);
    $f3->set('scripts',  $scripts);

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
    echo $template->render('views/_base.html');
});


// View New Recipe
$f3->route('GET|POST /recipe/@recipeID', function($f3, $params) {

    // Title to use in template.
    $title = $params['recipeID'];

    // List of paths to stylesheets.
    $styles = array(
        // If you need a stylesheet do
        //$f3->get('BASE').'/assets/styles/STYLESHEET-NAME.css
    );

    // List of paths for sub-templates being used.
    $includes = array(
        'views/_nav.html',
        'views/_recipe.html'
    );

    // List of paths to scripts being used.
    $scripts = array(
        // If you need a script do
        //$f3->get('BASE').'/assets/scripts/SCRIPT-NAME.js
    );

    $f3->set('title',    $title);
    $f3->set('styles',   $styles);
    $f3->set('includes', $includes);
    $f3->set('scripts',  $scripts);

    //SQL to get Recipe Name

    $f3->set('recipeID', $params['recipeID']);

    //print_r($_POST);

    if(isset($_POST['submit'])) {
        Model::login($_POST['username'],$_POST['password']);
    }



    $template = new Template();
    echo $template->render('views/_base.html');
});

$f3->route('GET /profiles/@user', function($f3, $params) {

    // Title to use in template.
    $title = $params['user'];

    // List of paths to stylesheets.
    $styles = array();

    // List of paths for sub-templates being used.
    $includes = array(
        'views/_nav.html',
        'views/_profile.html'
    );

    // List of paths to scripts being used.
    $scripts = array();

    $f3->set('title',    $title);
    $f3->set('styles',   $styles);
    $f3->set('includes', $includes);
    $f3->set('scripts',  $scripts);

    if(isset($_POST['submit'])) {
        Model::login($_POST['username'],$_POST['password']);
    }

    $template = new Template();
    echo $template->render('views/_base.html');
});


// Submit new Recipe
$f3->route('GET|POST /recipe/new-recipe', function($f3) {

    // Title to use in template.
    $title = "Submit your Recipe!";

    // List of paths to stylesheets.
    $styles = array(
        // If you need a stylesheet do
        //$f3->get('BASE').'/assets/styles/STYLESHEET-NAME.css
    );

    // List of paths for sub-templates being used.
    $includes = array(
        'views/_nav.html',
        'views/_submit-recipe.html'
    );

    // List of paths to scripts being used.
    $scripts = array(
        // If you need a script do
        //$f3->get('BASE').'/assets/scripts/SCRIPT-NAME.js
    );

    $f3->set('title',    $title);
    $f3->set('styles',   $styles);
    $f3->set('includes', $includes);
    $f3->set('scripts',  $scripts);

    //print_r($_POST);

    if(isset($_POST['submit'])) {
        Model::login($_POST['username'],$_POST['password']);
    }



    $template = new Template();
    echo $template->render('views/_base.html');
});


$f3->run();