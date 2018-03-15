<?php
/**
 * Author: Aaron Melhaff
 * Date: 3/15/2018
 * Time: 10:53 AM
 */

session_start();

require '../model.php';
require '../objects/user.php';

Model::connect();


// If User object has been stored in session, prepare
// a unserialized version to be used in functions.
if(isset($_SESSION['user'])) {
    $GLOBALS['user'] = unserialize($_SESSION['user']);
    if($GLOBALS['user']->getPrivilege() == "moderator" ||
       $GLOBALS['user']->getPrivilege() == "admin") {

        $userObject = new User(
            $_POST['userid'],
            $_PORT['username'],
            $_POST['email'],
            $_POST['privilege']
        );

        $userObject->resetPassword();

        var_dump($userObject);
    }


} else echo "Unauthorized";




