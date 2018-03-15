<?php
/**
 * User: Aaron Melhaff
*/

//Begin session
session_start();

ini_set('display_errors',1);
error_reporting(E_ALL);

// Require f3
require_once('vendor/autoload.php');

// Setup
$f3 = Base::instance();
$f3->set('DEBUG',3);

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
    $styles = array(
        $f3->get('BASE').'/assets/styles/login.css'
    );

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

// Submit new Recipe
$f3->route('GET|POST /recipe/new-recipe', function($f3) {

    //Post Array Test:
    print_r($_POST);

    /*
     * Array ( [recipeName] => Spaghetti [prepTime] => 5 [cookTime] => 15
     * [servs] => 4 [cals] => 150 [description] => A classic Italian masterpiece that you'll love
     * [ingreds] => Array ( [0] => 16oz ground beef [1] => meat sauce [2] => water [3] => salt [4]
     *      => pepper )
     * [directs] => Array ( [0] => Start boiling water [1] => cook ground beef and add meat sauce to it
     *      [2] => once finished w/ both, plate pasta then beef on top [3] => Enjoy ) )
     */

    //Insert Statement
    /*
     * INSERT INTO `recipe` (
     * `recipeid`, `name`, `prepTime`, `cookTime`, `servings`, `cal`
     * , `descript`, `ingredients`, `directions`, `likes`) VALUES
     * (NULL, 'Spaghetti', '5', '15', '4', '150',
     * 'A classic Italian dish that is both cheap to make and delicious.',
     * '16oz of Ground Beef, 16oz of Pasta Noodles, Salt, Pepper, Water, meatsauce',
     * 'Boil Water, Put noodles in water, fry ground beef in pan
     *      until cooked, add meat sauce to beef, once pasta is finished along with the
     *      beef add pasta to plate followed by beef on top',
     * '5');
     */

    if(isset($_POST['submit'])){

        Model::insertRecipe();

    }

    // Title to use in template.
    $title = "Submit your Recipe!";
    // List of paths to stylesheets.
    $styles = array(
        $f3->get('BASE').'/assets/styles/icons.css'
    );
    // List of paths for sub-templates being used.
    $includes = array(
        'views/_nav.html',
        'views/_submit-recipe.html'
    );
    // List of paths to scripts being used.
    $scripts = array(
        // If you need a script do
        $f3->get('BASE').'/assets/scripts/recipe-scripts.js'
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


// Submit Recipe route
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

// User Profile route
$f3->route('GET /profiles/@user', function($f3, $params) {

    // Reroute if not logged in!
    if(!Model::authorized()) {
        $f3->reroute('/');
    }

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

    // Store page attributes to hive.
    $f3->set('title',    $title);
    $f3->set('styles',   $styles);
    $f3->set('includes', $includes);
    $f3->set('scripts',  $scripts);

    // Make login attempt.
    if(isset($_POST['submit'])) {
        Model::login($_POST['username'],$_POST['password']);
    }

    // Display template.
    $template = new Template();
    echo $template->render('views/_base.html');
});

// Administration route.
$f3->route('GET /administration', function($f3) {

    // If user is not a mod or higher, return home.
    if(!Model::authorized(1)){
        $f3->reroute('/');
    }

    // Title to use in template.
    $title = "Administration";

    // List of paths to stylesheets.
    $styles = array(
        'https://cdn.datatables.net/v/bs4/dt-1.10.16/af-2.2.2/b-1.5.1/r-2.2.1/sl-1.2.5/datatables.min.css'
    );

    // List of paths for sub-templates being used.
    $includes = array(
        'views/_nav.html',
        'views/_administration.html'
    );

    // List of paths to scripts being used.
    $scripts = array(
        'https://cdn.datatables.net/v/bs4/dt-1.10.16/af-2.2.2/b-1.5.1/r-2.2.1/sl-1.2.5/datatables.min.js',
        $f3->get('BASE').'/assets/scripts/administration.js'
    );

    $results = Model::viewUsers();

    // Store variables in hive.
    $f3->set('title',    $title);
    $f3->set('styles',   $styles);
    $f3->set('includes', $includes);
    $f3->set('scripts',  $scripts);
    $f3->set('rows',     $results);

    // Display Template
    $template = new Template();
    echo $template->render('views/_base.html');
});


$f3->run();