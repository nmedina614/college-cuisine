<?php

/**
 * This file link to a folder in the root folder containing a file
 * with Constants named:
 * - DB_DSN
 * - DB_USERNAME
 * - DB_PASSWORD
 */
require $_SERVER['DOCUMENT_ROOT'] . "/../config/cc_config.php";

/**
 * Class Model
 *
 * Class used to handle logical operations and
 * database interactions.
 *
 * @author Aaron Melhaff <nash_melhaff@hotmail.com>
 * @author Nolan Medina <nmedina@mail.greenriver.edu>
 */
class Model
{
    // Variable containing database object.
    private static $_dbh;

    /**
     * Function used to initialise the $_dbh field.
     *
     * Takes constants posted in the config file
     * and uses then to instantiate a PDO database
     * object.
     */
    public static function connect()
    {
        // If there isn't already a database variable...
        if(!isset($_dbh)) {
            try {
                // instantiate pdo object.
                self::$_dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Method used to make login attempts.
     *
     * Takes posted username and password and
     * checks to see if there is a matching
     * user in the database. If the query
     * is a success, then it stores the
     * users information in the session as
     * a user object.
     *
     * @return bool Boolean representing whether the login was successful.
     */
    public static function login()
    {

        // Flush any old login sessions.
        session_reset();

        // Don't bother querying db if params are empty.
        if(empty($_POST['username']) || empty($_POST['password'])) {
            return false;
        }

        // Prepare a select to check if db contains queried params.
        $sql = 'SELECT userid, username, email, privilege FROM user 
                WHERE username=:username AND password=:password';

        $statement = self::$_dbh->prepare($sql);

        $statement->bindParam(':username', $_POST['username'], PDO::PARAM_STR);

        $statement->bindParam(':password', hash('sha256', $_POST['password'], false), PDO::PARAM_STR);

        $statement->execute();

        // Pull first result.
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        // If it isn't empty, store pulled info into session.
        if(isset($result['username'])) {

            // Store user information as user object in Session.
            $_SESSION['user'] =
                ($result['privilege'] == 'moderator' ||
                 $result['privilege'] == 'admin') ?

                    // If the users privilege level is mod or admin,
                    // store an admin object.
                    serialize(new Moderator(
                    $result['userid'],
                    $result['username'],
                    $result['email'],
                    $result['privilege'])) :

                    // Otherwise store a normal user.
                    serialize(new User(
                    $result['userid'],
                    $result['username'],
                    $result['email'],
                    $result['privilege']));


            return true;

        } else return false;



    }

    /**
     * Method for converting from privilege string
     * to authorization int.
     *
     * @param $privilege String found in user object.
     * @return int Integer used to compare different privilege types.
     */
    public static function getAuthority($privilege)
    {

        //Take string and return integer representing privilege value.
        switch($privilege) {
            case 'admin':
                return 2;

            case 'moderator':
                return 1;

            case 'basic':
                return 0;

            default:
                return -1;
        }

    }

    /**
     * Method for seeing if a user has a required authority level.
     *
     * Method that takes a number. If the privilege is
     * greater or equal to the input parameter, then it
     * returns true. Otherwise, it returns false.
     * The options are:
     * * -1: Not logged in or active.
     * * 0: basic log in.
     * * 1: moderator login.
     * * 2: admin login.
     *
     * @param $needed Authority level needed.
     * @return Returns whether the authority level is great enough.
     */
    public static function authorized($needed = 0)
    {

        if(empty($GLOBALS['user'])) return false;

        $authority = self::getAuthority($GLOBALS['user']->getPrivilege());

        return ($authority >= $needed);
    }

    /**
     * Method used to pull all users that are not admins.
     *
     * @return Returns an array of results.
     */
    public static function viewUsers()
    {
        if(self::authorized(1)) {
            $sql = 'SELECT * FROM user WHERE NOT privilege=\'admin\'';

            // Prepare query
            $statement = self::$_dbh->prepare($sql);

            // Execute.
            $statement->execute();

            // Return results of query.
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }

    }

    /**
     * Method used to change the password of the user with
     * the given id.
     *
     * @param $userid Integer id of the user whose password is being changed.
     * @param $newPassword String new password for the given user.
     */
    public static function updatePassword($userid, $newPassword)
    {
        // State query
        $updateQuery = 'UPDATE user SET password=SHA2(:newPassword, 256) WHERE userid=:userid';

        // Prepare database query.
        $statement = self::$_dbh->prepare($updateQuery);

        // Bind all parameters.
        $statement->bindParam(':newPassword', $newPassword, PDO::PARAM_STR);
        $statement->bindParam(':userid', $userid, PDO::PARAM_INT);

        // Launch Query.
        $statement->execute();
    }

    /**
     * Method for deleting users.
     *
     * @param $userid Integer id of user being deleted.
     * @return Returns true/false whether the user was removed.
     */
    public static function deleteUser($userid)
    {
        // State query
        $updateQuery = 'DELETE FROM `user` WHERE userid=:userid';

        // Prepare database query.
        $statement = self::$_dbh->prepare($updateQuery);

        // Bind all parameters.
        $statement->bindParam(':userid', $userid, PDO::PARAM_INT);

        // Launch Query.
        return $statement->execute();
    }

    /**
     * Method for sending emails to a given recipient.
     *
     * @param $recipient String email address to send to.
     * @param $subject String displayed at the top of the email.
     * @param $message String representing the body of the message.
     */
    public static function sendMessage($recipient, $subject, $message)
    {

        $subject = $subject;
        $txt = $message;
        $headers = 'From: college-cuisine <noreply@'.$_SERVER['HTTP_HOST'].'>';

        mail($recipient,$subject,$txt,$headers);
    }

    /**
     * Method for generating random password strings.
     *
     * Taken from https://stackoverflow.com/questions/1837432/how-to-generate-random-password-with-php
     *
     * @param $length Integer representing the length of the password string to generate.
     * @return string Returns a string random password of the given length.
     */
    public static function generatePassword($length = 64) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }


    /**
     * TODO
     */
    public static function insertRecipe($path) {

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
     * '5'); */

        // Prepare a select to check if db contains queried params.
        $sql = 'INSERT INTO `recipe` (`name`, `prepTime`, 
                `cookTime`, `servings`, `cal`, `descript`, `ingredients`, 
                `directions`, `likes`, `image`) VALUES (:recipeName, :prepTime, 
                :cookTime, :servings, :cal, :descript, :ingredients,
                :directions, \'0\', :image)';

        $statement = self::$_dbh->prepare($sql);

        /*
     * Array ( [recipeName] => Spaghetti [prepTime] => 5 [cookTime] => 15
     * [servs] => 4 [cals] => 150 [description] => A classic Italian masterpiece that you'll love
     * [ingreds] => Array ( [0] => 16oz ground beef [1] => meat sauce [2] => water [3] => salt [4]
     *      => pepper )
     * [directs] => Array ( [0] => Start boiling water [1] => cook ground beef and add meat sauce to it
     *      [2] => once finished w/ both, plate pasta then beef on top [3] => Enjoy ) )
     */

        echo $sql;

        echo $_POST['recipeName'];

        echo  implode(',',$_POST['ingreds']);

        $statement->bindParam(':recipeName', $_POST['recipeName'], PDO::PARAM_STR);
        $statement->bindParam(':prepTime', $_POST['prepTime'], PDO::PARAM_STR);
        $statement->bindParam(':cookTime', $_POST['cookTime'], PDO::PARAM_STR);
        $statement->bindParam(':servings', $_POST['servs'], PDO::PARAM_STR);
        $statement->bindParam(':cal', $_POST['cals'], PDO::PARAM_STR);
        $statement->bindParam(':descript', $_POST['description'], PDO::PARAM_STR);
        $statement->bindParam(':ingredients', implode(',',$_POST['ingreds']), PDO::PARAM_STR);
        $statement->bindParam(':directions', implode(',',$_POST['directs']), PDO::PARAM_STR);
        $statement->bindParam(':image', $path, PDO::PARAM_STR);

        $statement->execute();

    }

    /**
     * TODO
     */
    public static function getAllRecipes()
    {
        // State query
        $sql = 'SELECT * FROM recipe order by likes DESC';

        // Prepare database query.
        $statement = self::$_dbh->prepare($sql);

        // Launch Query.
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * TODO
     * @param $id
     * @return mixed
     */
    public static function getRecipe($id)
    {
        // State query
        $sql = 'SELECT * FROM recipe WHERE recipeid = :id';

        // Prepare database query.
        $statement = self::$_dbh->prepare($sql);

        //Bind Params
        $statement->bindParam(':id', $id, PDO::PARAM_INT);


        // Launch Query.
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Method for changing a users privilege level.
     *
     * @param $targetID Integer id of user being altered.
     * @param $newPrivilege String new level to be assigned.
     */
    public static function reassignUser($targetID, $newPrivilege)
    {
        if(self::authorized(1)) {
            $sql = 'UPDATE user SET privilege=:privilege WHERE userid=:userid';

            $statement = self::$_dbh->prepare($sql);

            $statement->bindParam(':privilege', $newPrivilege, PDO::PARAM_INT);
            $statement->bindParam(':userid', $targetID, PDO::PARAM_INT);

            $statement->execute();

        } else echo 'Access Denied!';
    }

    /**
     * TODO
     */
    public static function likeRecipe($recipeID, $userID)
    {
        // State query
        $sql = 'UPDATE `recipe` SET `likes` = `likes` + 1 WHERE `recipe`.`recipeid` = :recipeID';

        // Prepare database query.
        $statement = self::$_dbh->prepare($sql);

        //Bind Params
        $statement->bindParam(':recipeID', $recipeID, PDO::PARAM_INT);


        // Launch Query.
        $statement->execute();

        // State query
        $sql = 'INSERT INTO `liked-recipes` (`userID`, `recipeID`) VALUES (:userID, :recipeID)';

        // Prepare database query.
        $statement = self::$_dbh->prepare($sql);

        //Bind Params
        $statement->bindParam(':recipeID', $recipeID, PDO::PARAM_INT);
        $statement->bindParam(':userID', $userID, PDO::PARAM_INT);


        // Launch Query.
        $statement->execute();

        // State query
        $sql = 'DELETE FROM `dislike-recipe` WHERE userID=:userID AND recipeID=:recipeID';

        // Prepare database query.
        $statement = self::$_dbh->prepare($sql);

        //Bind Params
        $statement->bindParam(':userID', $userID, PDO::PARAM_INT);
        $statement->bindParam(':recipeID', $recipeID, PDO::PARAM_INT);


        // Launch Query.
        $statement->execute();

    }

    /**
     * TODO
     */
    public static function dislikeRecipe($recipeID, $userID)
    {
        // State query
        $sql = 'UPDATE `recipe` SET `likes` = `likes` - 1 WHERE `recipe`.`recipeid` = :recipeID';

        // Prepare database query.
        $statement = self::$_dbh->prepare($sql);

        //Bind Params
        $statement->bindParam(':recipeID', $recipeID, PDO::PARAM_INT);


        // Launch Query.
        $statement->execute();

        // State query
        $sql = 'INSERT INTO `dislike-recipe` (`userID`, `recipeID`) VALUES (:userID, :recipeID)';

        // Prepare database query.
        $statement = self::$_dbh->prepare($sql);

        //Bind Params
        $statement->bindParam(':recipeID', $recipeID, PDO::PARAM_INT);
        $statement->bindParam(':userID', $userID, PDO::PARAM_INT);


        // Launch Query.
        $statement->execute();

        // State query
        $sql = 'DELETE FROM `liked-recipes` WHERE userID=:userID AND recipeID=:recipeID';

        // Prepare database query.
        $statement = self::$_dbh->prepare($sql);

        //Bind Params
        $statement->bindParam(':userID', $userID, PDO::PARAM_INT);
        $statement->bindParam(':recipeID', $recipeID, PDO::PARAM_INT);


        // Launch Query.
        $statement->execute();

    }

    /**
     * Method for registering a new user.
     *
     * Takes input data from POST array.
     * If the fields are valid, it adds
     * them to the database. If not,
     * it returns an array of strings
     * containing reasons for failure.
     *
     * @return array Returns an array of strings containing reasons for failure.
     */
    public static function register() {

        // Store post fields in variables for sanity sake.
        $username  = $_POST['username'];
        $password1 = $_POST['password1'];
        $password2 = $_POST['password2'];
        $email     = $_POST['email'];

        // Validate params and get invalid in array.
        $invalid = Validator::validateRegistration($username, $password1, $password2, $email);

        if(count($invalid) == 0) {
            // State query
            $sql = 'INSERT INTO `user`(`username`, `password`, `email`, `privilege`) 
                    VALUES (:username, :password, :email, \'deactivated\')';

            // Prepare database query.
            $statement = self::$_dbh->prepare($sql);

            //Bind Params
            $statement->bindParam(':username', $username, PDO::PARAM_STR);
            $statement->bindParam(':password', hash('sha256', $password1), PDO::PARAM_STR);
            $statement->bindParam(':email',    $email,    PDO::PARAM_STR);

            // Launch Query.
            $success = $statement->execute();

            // If the user is inserted, use the new users id to create a verification hash
            // and store it in the db.
            if($success) {
                $userid = self::$_dbh->lastInsertId();
                $hash = hash('sha256', self::generatePassword());

                $verifyInsert = 'INSERT INTO `verification`(`userid`, `verifyCode`) VALUES (:userid, :hash)';

                $stmt = self::$_dbh->prepare($verifyInsert);
                $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                $stmt->bindParam(':hash',   $hash,   PDO::PARAM_STR);

                $result = $stmt->execute();

                // If the hash is stored successfully, send an email with the hash.
                if($result) {
                    self::sendMessage($email, 'Account verification',
                        'Thank you for signing up with College-Cuisine!
                        in order to activate your account, please open the following link. 
                        ' . ($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']) ."/verify/$hash"
                    );
                }
            } else {
                $invalid[] = 'Username or email is already in use.';
            }
        }

        // Return list of invalid inputs.
        return $invalid;



    }

    /**
     * Method for verifying an account email.
     *
     * Takes a given hash and checks that there
     * is a corresponding hash in the database.
     * If there is, it takes the userid associated with
     * the hash and changes the users privilege level to
     * basic. Then deletes the hash from the db.
     *
     * @param $hash String verification hash being compared.
     */
    public static function verifyAccount($hash)
    {
        $sql = 'SELECT userid FROM verification WHERE verifyCode=:hash';

        $searchQuery = self::$_dbh->prepare($sql);

        $searchQuery->bindParam(':hash', $hash, PDO::PARAM_STR);

        $searchQuery->execute();

        $result = $searchQuery->fetch();

        // If result comes back positive, then activate account.
        if(isset($result['userid'])) {
                $userid = $result['userid'];
                $sql2 = 'UPDATE user SET privilege=\'basic\' WHERE userid=:userid';

                $updateQuery = self::$_dbh->prepare($sql2);

                $updateQuery->bindParam(':userid', $userid, PDO::PARAM_INT);

                $success = $updateQuery->execute();

                $sql3 = 'DELETE FROM verification WHERE verifyCode=:hash';

                $deleteQuery = self::$_dbh->prepare($sql3);

                $deleteQuery->bindParam(':hash', $hash, PDO::PARAM_STR);

                return $deleteQuery->execute();

        } else return false;
    }

    /**
     * TODO
     *
     * @param $userID
     * @param $recipeID
     * @return bool
     */
    public static function validateLike($userID, $recipeID){


        $sql = 'SELECT * FROM `liked-recipes` WHERE userID = :userID AND recipeID = :recipeID';

        $searchQuery = self::$_dbh->prepare($sql);

        $searchQuery->bindParam(':userID', $userID, PDO::PARAM_STR);
        $searchQuery->bindParam(':recipeID', $recipeID, PDO::PARAM_STR);

        $searchQuery->execute();

        $result = $searchQuery->rowCount();

        if($result > 0){

            return false;

        } else {
            return true;
        }

    }

    /**
     * TODO
     *
     * @param $userID
     * @param $recipeID
     * @return bool
     */
    public static function validateDislike($userID, $recipeID){

        $sql = 'SELECT * FROM `dislike-recipe` WHERE userID = :userID AND recipeID = :recipeID';

        $searchQuery = self::$_dbh->prepare($sql);

        $searchQuery->bindParam(':userID', $userID, PDO::PARAM_STR);
        $searchQuery->bindParam(':recipeID', $recipeID, PDO::PARAM_STR);

        $searchQuery->execute();

        $result = $searchQuery->rowCount();

        if($result > 0){

            return false;

        } else {
            return true;
        }

    }


}