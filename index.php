<?php
/**
 * Authors: Aaron Melhaff, Nolan Medina
 */

//Begin session
session_start();

ini_set('display_errors',1);
error_reporting(E_ALL);

// Require f3
require_once('vendor/autoload.php');



// Setup
$f3 = Base::instance();
$GLOBALS['target_file'] = "";

// If User object has been stored in session, prepare
// a unserialized version to be used in functions.
if(isset($_SESSION['user'])) {
    $GLOBALS['user'] = unserialize($_SESSION['user']);
    $f3->set('authority', Model::getAuthority($GLOBALS['user']->getPrivilege()));
} else {
    $f3->set('authority', -1);
}
$f3->set('DEBUG',3);

// Establish database connection.
Model::connect();

//Random Recipe
$recipes  = Model::getAllRecipes();

$f3->set('rand', $recipes[rand(0,sizeof($recipes))]['recipeid']);

// Homepage route.
$f3->route('GET /', function($f3, $recipes) {

    // Title to use in template.
    $title = "College Cuisine";


    // List of paths to stylesheets.
    $styles = array(
        'assets/styles/home.css'
    );

    // List of paths for sub-templates being used.
    $includes = array(
        'views/_nav.html',
        'views/_home.html'
    );

    $results = Model::getAllRecipes();

    // List of paths to scripts being used.
    $scripts = array();

    $f3->set('title',    $title);
    $f3->set('styles',   $styles);
    $f3->set('includes', $includes);
    $f3->set('scripts',  $scripts);

    $f3->set('recipes', $results);

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

    //Checks to see if the user is logged in or not to submit a recipe
    if(!isset($_SESSION['user'])) {

        $f3->reroute('/login');

    }

    //Post Array
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

        //Set Fat Free variables for Sticky
        $f3->set('recipeName', $_POST['recipeName']);
        $f3->set('prepTime', $_POST['prepTime']);
        $f3->set('cookTime', $_POST['cookTime']);
        $f3->set('servings', $_POST['servs']);
        $f3->set('calories', $_POST['cals']);
        $f3->set('description', $_POST['description']);
        $f3->set('ingreds', $_POST['ingreds']);
        $f3->set('directs', $_POST['directs']);

        //See if there is any validation errors for inputs
        $errors = Validator::validateRecipe();

        //If no validation errors...
        if($errors == null) {

            //get the path for the file
            $path = $GLOBALS['target_file'];

            //upload the recipe to the database
            Model::insertRecipe($path, $GLOBALS['user']->getUserid());


            //Reroute to homepage
            $f3->reroute('/');

        } else {

            $f3->set('errors', $errors);

        }

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
        $f3->get('BASE').'/assets/scripts/recipe-scripts.js',

        //testings php stickiness
        $f3->get('BASE').'/assets/scripts/validate-recipe.js'
    );
    $f3->set('title',    $title);
    $f3->set('styles',   $styles);
    $f3->set('includes', $includes);
    $f3->set('scripts',  $scripts);
    //print_r($_POST); TODO
    if(isset($_POST['submit'])) {
        Model::login($_POST['username'],$_POST['password']);
    }
    $template = new Template();
    echo $template->render('views/_base.html');
});


// Submit Recipe route
$f3->route('GET|POST /recipe/@recipeID', function($f3, $params) {

    $_SESSION['recipeID'] = $params['recipeID'];

    //See if user clicked like!
    if(isset($_POST['like'])) {

        //see if the user is logged in and has enough privilege
        if(isset($_SESSION['user']) && $GLOBALS['user']->getPrivilege() >= 0) {


            if(!Model::validateLike($GLOBALS['user']->getUserid(), $params['recipeID'])){
                $f3->set('error', "You have already liked this recipe!");
            } else {
                //Like the Recipe!
                Model::likeRecipe($params['recipeID'], $GLOBALS['user']->getUserid());
                $f3->set('success', "You have liked this recipe!");
            }
        } else {
            $f3->set('error', "You must be logged in to like a recipe!");
        }

    } else if(isset($_POST['dislike'])) {

        //see if the user is logged in and has enough privilege
        if(isset($_SESSION['user']) && $GLOBALS['user']->getPrivilege() >= 0) {

            if(!Model::validateDislike($GLOBALS['user']->getUserid(), $params['recipeID'])){
                $f3->set('error', "You have already disliked this recipe!");
            } else {
                //dislike the Recipe!
                Model::dislikeRecipe($params['recipeID'], $GLOBALS['user']->getUserid());
                $f3->set('success', "You have disliked this recipe!");
            }
        } else {
            $f3->set('error', "You must be logged in to dislike a recipe!");
        }

    }

    //Gets the Recipe from the database.
    $result = Model::getRecipe($params['recipeID']);

    // Title to use in template.
    $title = $result['name'];

    // List of paths to stylesheets.
    $styles = array(
        // If you need a stylesheet do
        $f3->get('BASE').'/assets/styles/recipe.css'
    );

    // List of paths for sub-templates being used.
    $includes = array(
        'views/_nav.html',
        'views/_recipe.html'
    );

    // List of paths to scripts being used.
    $scripts = array(
        // If you need a script do
        $f3->get('BASE').'/assets/scripts/recipe-scripts.js'
    );


    //Sets Fat Free variables to use in HTML
    $f3->set('title',    $title);
    $f3->set('styles',   $styles);
    $f3->set('includes', $includes);
    $f3->set('scripts',  $scripts);

    $f3->set('recipe',  $result);
    $f3->set('ingredients', explode(",", $result['ingredients']));
    $f3->set('directions', explode(",", $result['directions']));
    $f3->set('image',  $result['image']);


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

    // Display template.
    $template = new Template();
    echo $template->render('views/_base.html');
});

// User Profile route
$f3->route('GET /profile/reset-password', function($f3, $params) {

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

$f3->route('GET|POST /registration', function($f3) {

    $included = 'views/_registration.html';

    if(isset($_POST['submit'])) {
        $invalid = Model::register();
        if(count($invalid) == 0) {
            $included = 'views/_confirmation.html';
        }
        $f3->set('invalid', $invalid);
    }

    // Title to use in template.
    $title = "Register";

    // List of paths to stylesheets.
    $styles = array();

    // List of paths for sub-templates being used.
    $includes = array(
        'views/_nav.html',
        $included
    );

    // List of paths to scripts being used.
    $scripts = array();

    // Store page attributes to hive.
    $f3->set('title',    $title);
    $f3->set('styles',   $styles);
    $f3->set('includes', $includes);
    $f3->set('scripts',  $scripts);

    // Display Template
    $template = new Template();
    echo $template->render('views/_base.html');
});

// Route for link for verifying new users.
$f3->route('GET /registration/verify/@hash', function($f3, $params) {
    if(isset($_SESSION['user']) && $GLOBALS['user']->getPrivilege() >= 0) {
        $f3->reroute('/');
    }

    $hash = $params['hash'];

    $result = Model::verifyAccount($hash);


    // Title to use in template.
    $title = "Verify Account";

    // List of paths to stylesheets.
    $styles = array();

    // List of paths for sub-templates being used.
    $includes = array(
        'views/_nav.html',
        'views/_verification.html'
    );

    // List of paths to scripts being used.
    $scripts = array();

    // Store page attributes to hive.
    $f3->set('result',   $result);
    $f3->set('title',    $title);
    $f3->set('styles',   $styles);
    $f3->set('includes', $includes);
    $f3->set('scripts',  $scripts);

    // Display Template
    $template = new Template();
    echo $template->render('views/_base.html');
});

// Route for link for verifying new users.
$f3->route('GET /registration/verify/@hash', function($f3, $params) {
    if(isset($_SESSION['user']) && $GLOBALS['user']->getPrivilege() >= 0) {
        $f3->reroute('/');
    }

    $hash = $params['hash'];

    $result = Model::verifyAccount($hash);


    // Title to use in template.
    $title = "Verify Account";

    // List of paths to stylesheets.
    $styles = array();

    // List of paths for sub-templates being used.
    $includes = array(
        'views/_nav.html',
        'views/_verification.html'
    );

    // List of paths to scripts being used.
    $scripts = array();

    // Store page attributes to hive.
    $f3->set('result',   $result);
    $f3->set('title',    $title);
    $f3->set('styles',   $styles);
    $f3->set('includes', $includes);
    $f3->set('scripts',  $scripts);

    // Display Template
    $template = new Template();
    echo $template->render('views/_base.html');
});


$f3->run();