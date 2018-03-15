<?php

/*
 * This file should contain domain specific login credentials:
 * DB_DSN
 * DB_USERNAME
 * DB_PASSWORD
 */
require $_SERVER['DOCUMENT_ROOT'] . "/../config/cc_config.php";

/**
 * Class Model
 *
 * TODO
 */
class Model
{
    // Variable containing database object.
    private static $_dbh;

    /**
     * TODO
     */
    public static function connect()
    {
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
     * TODO
     *
     * @return bool
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

            // Store user information in Session.
//            $_SESSION['userid'] = $result['userid'];
//            $_SESSION['username'] = $result['username'];
//            $_SESSION['email'] = $result['email'];
//            $_SESSION['privilege'] = $result['privilege'];
            $_SESSION['user'] = serialize(new User(
                $result['userid'],
                $result['username'],
                $result['email'],
                $result['privilege']
            ));

            return true;

        } else return false;



    }

    /**
     * Method that takes a number. If the privilege is
     * greater or equal to the input parameter, then
     * return true. Otherwise, return false. The options are:
     * 0: basic log in.
     * 1: moderator login.
     * 2: admin login.
     *
     * @param $needed Authority level needed.
     * @return Returns whether the authority level is great enough.
     */
    public static function authorized($needed = 0)
    {

        if(empty($GLOBALS['user'])) return false;

        $authority =
            ($GLOBALS['user']->getPrivilege() == 'admin')     ? 2 :
            ($GLOBALS['user']->getPrivilege() == 'moderator') ? 1 : 0;

        return ($authority >= $needed);
    }

    /**
     * TODO
     */
    public static function viewUsers()
    {
        if(self::authorized(1)) {
            $sql = 'SELECT * FROM user WHERE privilege=\'basic\'';

            // Prepare query
            $statement = self::$_dbh->prepare($sql);

            // Execute.
            $statement->execute();

            // Return results of query.
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }

    }

    /**
     * TODO
     *
     * @param $userid
     */
    public static function resetPassword($userid)
    {
        $newPassword = self::generatePassword();

        $updateQuery = 'UPDATE user SET password=:newPassword WHERE userid=SHA2(:userid, 256)';

        $statement = self::$_dbh->prepare($updateQuery);

        $statement->bindParam(':newPassword', $newPassword, PDO::PARAM_STR);
        $statement->bindParam(':userid', $userid, PDO::PARAM_INT);

        $statement->execute();

        // Then get email.

        $emailQuery = "SELECT email FROM user WHERE userid='$userid'";

        $result = self::$_dbh->query($emailQuery);

        $targetEmail = $result->fetch(PDO::FETCH_ASSOC)['email'];


        self::sendPassword($targetEmail, $newPassword, 'noreply@amelhaff.greenriverdev.com');
    }

    /**
     * TODO
     *
     * @param $recipient
     * @param $password
     * @param $sender
     */
    public static function sendPassword($recipient, $password, $sender)
    {

        $subject = "Password reset";
        $txt = "Your new email is $password";
        $headers = "From:$sender";

        mail($recipient,$subject,$txt,$headers);
    }

    /**
     * TODO
     *
     * Taken from https://stackoverflow.com/questions/1837432/how-to-generate-random-password-with-php
     *
     * @param int $length
     * @return string
     */
    public static function generatePassword($length = 8) {
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
    public static function verifyHash() {
        // Generate random 32 character hash and assign it to a local variable.
        return md5( rand(0,1000));
    }

    public static function insertRecipe() {

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
                `directions`, `likes`) VALUES (:recipeName, :prepTime, 
                :cookTime, :servings, :cal, :descript, :ingredients,
                :directions, \'0\')';

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

        $statement->execute();

    }

}
?>