<?php
/**
 * Script used to handle promotion requests.
 *
 * @author Aaron Melhaff <nash_melhaff@hotmail.com>
 * @since 3/15/2018
 */

session_start();

require '../model.php';
require '../objects/user.php';
require '../objects/moderator.php';

Model::connect();


// If User object has been stored in session, prepare
// a non-serialized version to be used in functions.
if(isset($_SESSION['user'])) {
    $GLOBALS['user'] = unserialize($_SESSION['user']);
    if($GLOBALS['user']->getPrivilege() == "admin") {

        $userObject = new User(
            $_POST['userid'],
            $_POST['username'],
            $_POST['email'],
            $_POST['privilege']
        );

        // Must have higher authority to change authority.
        if(Model::getAuthority($GLOBALS['user']->getPrivilege()) > $userObject->getPrivilege()) {
            $GLOBALS['user']->promote($userObject->getUserid());
            echo 'Promoting user '.$userObject->getUsername();
        } else echo "You do not have the authority to perform this action.";

    }


} else echo "Unauthorized";




