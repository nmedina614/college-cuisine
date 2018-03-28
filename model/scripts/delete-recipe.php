<?php
/**
 * Script to delete recipe from the recipe tables
 *
 * User: Nolan Medina
 * @author Nolan Medina <njmedina614@gmail.com>
 * @since 3/17/2018
 */

//Starts Session to get User data
session_start();

//require needed files
require '../model.php';
require '../objects/user.php';
require '../objects/moderator.php';

//Connects to DB
Model::connect();

//Checks to see if user is logged in
if(isset($_SESSION['user'])) {
    //Sets User object to global variable
    $GLOBALS['user'] = unserialize($_SESSION['user']);

    //Checks the user privilege or if the user created the recipe.
    if($GLOBALS['user']->getPrivilege() == "moderator" ||
        $GLOBALS['user']->getPrivilege() == "admin" ||
        //Method to check if user created the recipe.
        Model::validateDelete($GLOBALS['user']->getUserid(),$_SESSION['recipeID'])) {

        //Deletes the recipe if everything is ok
        Model::deleteRecipe();

        //Response to the user.
        echo 'Recipe Deleted, Redirecting to home page';

    } else echo "Unauthorized";

    //Unauthorized account if tries to delete.
} else echo "Unauthorized";

