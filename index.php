<?php
/**
 * File used for controlling website navigation and routing.
 *
 * @author Aaron Melhaff
 * @author Nolan Medina
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

        $result = Model::login($_POST['username'], $_POST['password']);

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
$f3->route('GET|POST /profiles/@user/reset-password', function($f3, $params) {

    // Process posted form.
    if(isset($_POST['submit'])) {

        $oldPassword  = $_POST['oldPassword'];
        $newPassword1 = $_POST['newPassword1'];
        $newPassword2 = $_POST['newPassword2'];

        $invalid = $GLOBALS['user']->changePassword($oldPassword, $newPassword1, $newPassword2);

        if(count($invalid) === 0) {

            $f3->reroute('/profiles/' . $GLOBALS['user']->getUsername());

        } else {
            // Store list of failure conditions in hive.
            $f3->set('invalid', $invalid);
        }
    }

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
        'views/_reset-password.html'
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
$f3->route('GET|POST /profiles/@user/change-email', function($f3, $params) {

    // Process posted form.
    if(isset($_POST['submit'])) {

        $newEmail = $_POST['newEmail'];
        $invalid  = $GLOBALS['user']->changeEmail($newEmail);

        if(empty($invalid)) {

            $f3->reroute('/profiles/' . $GLOBALS['user']->getUsername());

        } else {
            // Store list of failure conditions in hive.
            $f3->set('invalid', $invalid);
        }
    }

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
        'views/_change-email.html'
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

$f3->run();