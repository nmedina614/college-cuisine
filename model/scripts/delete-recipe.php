<?php
/**
 * Created by PhpStorm.
 * User: njmed
 * Date: 3/18/2018
 * Time: 2:31 AM
 */

session_start();

require '../model.php';
require '../objects/user.php';
require '../objects/moderator.php';

Model::connect();

if(isset($_SESSION['user'])) {
    $GLOBALS['user'] = unserialize($_SESSION['user']);
    if($GLOBALS['user']->getPrivilege() == "moderator" ||
        $GLOBALS['user']->getPrivilege() == "admin" ||
        Model::validateDelete($GLOBALS['user']->getUserid(),$_SESSION['recipeID'])) {

        Model::deleteRecipe();
        echo 'Recipe Deleted, Redirecting to home page';

    }

} else echo "Unauthorized";

