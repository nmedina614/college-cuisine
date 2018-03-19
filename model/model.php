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
 * @author Nolan Medina <njmedina614@gmail.com>
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
    public static function login($username, $password)
    {

        // Flush any old login sessions.
        session_reset();

        // Don't bother querying db if params are empty.
        if(empty($username) || empty($password)) {
            return false;
        }

        // Prepare a select to check if db contains queried params.
        $sql = 'SELECT userid, username, email, privilege FROM user 
                WHERE username=:username AND password=:password';

        $statement = self::$_dbh->prepare($sql);

        $statement->bindParam(':username', $username, PDO::PARAM_STR);

        $statement->bindParam(':password', hash('sha256', $password, false), PDO::PARAM_STR);

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
     * Method for making user confirm password for security reasons.
     *
     * @param $password
     * @return boolean Returns true or false whether attempt is successful.
     */
    public static function confirmPassword($password)
    {
        // Statement to prepare.
        $sql = 'SELECT userid FROM user WHERE userid=:userid AND password=:password';

        // Prepare statement.
        $stmt = self::$_dbh->prepare($sql);

        // Pull username from unserialized session variable.
        $userid   = $GLOBALS['user']->getUserid();

        // Hash password passed in.
        $passHash = hash('sha256', $password);

        // Bind all userid and hashed password.
        $stmt->bindParam(':userid',   $userid,   PDO::PARAM_INT);
        $stmt->bindParam(':password', $passHash, PDO::PARAM_STR);

        // Execute Query.
        $stmt->execute();

        // Pull first result.
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return true if result set is not empty
        return isset($result['userid']);
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
     * @param $needed int Authority level needed.
     * @return mixed - Returns whether the authority level is great enough.
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
     * @return array Returns an array of results.
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
        $updateQuery = 'UPDATE user SET password=:newPassword WHERE userid=:userid';

        // Prepare database query.
        $statement = self::$_dbh->prepare($updateQuery);

        // Bind all parameters.
        $statement->bindParam(':newPassword', hash('sha256', $newPassword, false), PDO::PARAM_STR);
        $statement->bindParam(':userid', $userid, PDO::PARAM_INT);

        // Launch Query.
        $statement->execute();
    }

    /**
     * Method used to change the password of the user with
     * the given id.
     *
     * @param $userid Integer id of the user whose password is being changed.
     * @param $newEmail String new password for the given user.
     * @return mixed - Row from Database
     */
    public static function changeEmail($userid, $newEmail)
    {
        // State query
        $updateQuery = 'UPDATE user SET email=:email WHERE userid=:userid';

        // Prepare database query.
        $statement = self::$_dbh->prepare($updateQuery);

        // Bind all parameters.
        $statement->bindParam(':email', $newEmail, PDO::PARAM_STR);
        $statement->bindParam(':userid', $userid, PDO::PARAM_INT);

        // Launch Query.
        return $statement->execute();
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
     * Function to submit recipe to Database
     *
     * This function takes a image path and the user ID to insert into 2 database
     * tables, one for the recipe with all the data from the form and the other
     * showing who submitted the recipe.
     *
     * @param $path - Image path that is the name of the image the user uploaded
     * @param $userID - The user id of the user uploading the recipe.
     */
    public static function insertRecipe($path, $userID) {

        // Prepare a select to check if db contains queried params.
        $sql = 'INSERT INTO `recipe` (`name`, `prepTime`, 
                `cookTime`, `servings`, `cal`, `descript`, `ingredients`, 
                `directions`, `likes`, `image`) VALUES (:recipeName, :prepTime, 
                :cookTime, :servings, :cal, :descript, :ingredients,
                :directions, \'0\', :image)';

        //Prepare Statement
        $statement = self::$_dbh->prepare($sql);

        //Bind Params
        $statement->bindParam(':recipeName', $_POST['recipeName'], PDO::PARAM_STR);
        $statement->bindParam(':prepTime', $_POST['prepTime'], PDO::PARAM_STR);
        $statement->bindParam(':cookTime', $_POST['cookTime'], PDO::PARAM_STR);
        $statement->bindParam(':servings', $_POST['servs'], PDO::PARAM_STR);
        $statement->bindParam(':cal', $_POST['cals'], PDO::PARAM_STR);
        $statement->bindParam(':descript', $_POST['description'], PDO::PARAM_STR);
        $statement->bindParam(':ingredients', implode(',',$_POST['ingreds']), PDO::PARAM_STR);
        $statement->bindParam(':directions', implode(',',$_POST['directs']), PDO::PARAM_STR);
        $statement->bindParam(':image', $path, PDO::PARAM_STR);

        //Execute statement
        $statement->execute();

        //Get the recipe ID
        $sql = 'SELECT * FROM recipe ORDER BY recipeID DESC';

        //Prepare statement
        $stat2 = self::$_dbh->prepare($sql);

        //Execute Statement
        $stat2->execute();

        //gets the recipe ID
        $recipe = $stat2->fetch(PDO::FETCH_ASSOC);

        //Uses the recipeID and the User ID to insert into table
        $sql = 'INSERT INTO `user-recipe` (`userID`, `recipeID`) VALUES (:userID, :recipeID)';

        //Prepare statement
        $stat3 = self::$_dbh->prepare($sql);

        //Bind Params
        $stat3->bindParam(':userID', $userID, PDO::PARAM_INT);
        $stat3->bindParam(':recipeID', $recipe['recipeid'], PDO::PARAM_INT);

        //execute statement
        $stat3->execute();

    }

    /**
     * Gets all the recipes from the database
     *
     * Function that access the database and recieves all the
     * recipes and then returns an array full of all of those
     * recipes to then be looped and viewed on the home page.
     *
     * @return mixed Array that holds all recipes.
     */
    public static function getAllRecipes()
    {
        // State query
        $sql = 'SELECT * FROM recipe order by likes DESC';

        // Prepare database query.
        $statement = self::$_dbh->prepare($sql);

        // Launch Query.
        $statement->execute();

        //return results from query.
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Gets recipe based on ID
     *
     * Function that returns a recipe based on the
     * id given to the function and returns that recipe
     * to be viewed.
     *
     * @param $id - Recipe ID
     * @return mixed - Recipe row from Database
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

        //Return row
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
     * Likes the recipe
     *
     * function that updates the recipe table row based on the
     * recipe ID and increments the like status by adding 1, also
     * adds to a like-recipes table to know if the user has already
     * liked the recipe.
     *
     * @param $recipeID - the ID for the recipe
     * @param $userID - the ID of the user
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
     * Likes the recipe
     *
     * function that updates the recipe table row based on the
     * recipe ID and decrementing the like status by subtracting 1, also
     * adds to a dislike-recipe table to know if the user has already
     * disliked the recipe.
     *
     * @param $recipeID - the ID for the recipe
     * @param $userID - the ID of the user
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
     * @return mixed true or false based on if statement executed correctly
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
     * Makes sure the User can like the recipe.
     *
     * Validates that the user can like the recipe by
     * checking the like recipes table to see if the user
     * has already liked the recipe
     *
     * @param $userID - Users ID
     * @param $recipeID - Recipe ID
     * @return bool - True or False based on if the user liked the recipe or not.
     */
    public static function validateLike($userID, $recipeID){

        //SQL statement
        $sql = 'SELECT * FROM `liked-recipes` WHERE userID = :userID AND recipeID = :recipeID';

        //Prepare Statement
        $searchQuery = self::$_dbh->prepare($sql);


        //Bind Params
        $searchQuery->bindParam(':userID', $userID, PDO::PARAM_STR);
        $searchQuery->bindParam(':recipeID', $recipeID, PDO::PARAM_STR);

        //Execute Query
        $searchQuery->execute();

        //Checks to see if its in the table
        $result = $searchQuery->rowCount();

        //Returns false to say user cant like recipe
        if($result > 0){

            return false;

        //returns true to say user can like recipe
        } else {
            return true;
        }

    }

    /**
     * Makes sure the User can dislike the recipe.
     *
     * Validates that the user can dislike the recipe by
     * checking the dislike recipes table to see if the user
     * has already disliked the recipe
     *
     * @param $userID - Users ID
     * @param $recipeID - Recipe ID
     * @return bool - True or False based on if the user disliked the recipe or not.
     */
    public static function validateDislike($userID, $recipeID){


        //SQL query
        $sql = 'SELECT * FROM `dislike-recipe` WHERE userID = :userID AND recipeID = :recipeID';

        //Prepare Query
        $searchQuery = self::$_dbh->prepare($sql);

        //Bind Params
        $searchQuery->bindParam(':userID', $userID, PDO::PARAM_STR);
        $searchQuery->bindParam(':recipeID', $recipeID, PDO::PARAM_STR);


        //Execute
        $searchQuery->execute();

        //Get row count
        $result = $searchQuery->rowCount();


        //Return false to say user cannot dislike recipe
        if($result > 0){

            return false;

        //return true if the user can dislike the recipe
        } else {
            return true;
        }

    }



    /**
     * Deletes the Recipe from the database
     *
     * Deletes the Recipe from the database and the user-recipeid table.
     */
    public static function deleteRecipe() {

        //Sql query
        $sql = 'DELETE FROM `user-recipe` WHERE `recipeID` = :recipeID';

        //Prepare Query
        $statement = self::$_dbh->prepare($sql);

        //Bind params
        $statement->bindParam(':recipeID', $_SESSION['recipeID'], PDO::PARAM_INT);

        //Executes Delete
        $statement->execute();

        //SQL query
        $sql = 'DELETE FROM `recipe` WHERE `recipeID` = :recipeID';

        //Prepare statement
        $statement = self::$_dbh->prepare($sql);

        //Bind Params
        $statement->bindParam(':recipeID', $_SESSION['recipeID'], PDO::PARAM_INT);

        //Execute Delete
        $statement->execute();
    }

    /**
     * Validates if the user can delete the recipe
     *
     * Checks to see if the user is the one that submitted the
     * recipe, if so return true, else return false
     *
     * @param $userID - ID of the user
     * @param $recipeID - Recipe ID
     * @return bool - True or false if user submitted recipe or not
     */
    public static function validateDelete($userID, $recipeID){

        //SQL query
        $sql = 'SELECT * FROM `user-recipe` WHERE `userID`= :userID AND `recipeID` = :recipeID';

        //Prepare Query
        $searchQuery = self::$_dbh->prepare($sql);

        //Bind Params
        $searchQuery->bindParam(':userID', $userID, PDO::PARAM_STR);
        $searchQuery->bindParam(':recipeID', $recipeID, PDO::PARAM_STR);


        //Execute
        $searchQuery->execute();

        //Get row count
        $result = $searchQuery->rowCount();


        //Return true to say user can delete the recipe
        if($result > 0){

            return true;

            //return false if the user cannot delete the recipe
        } else {
            return false;
        }


    }

}