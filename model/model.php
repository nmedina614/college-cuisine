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
    public static function login() {

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
            $_SESSION['userid'] = $result['userid'];
            $_SESSION['username'] = $result['username'];
            $_SESSION['email'] = $result['email'];
            $_SESSION['privilege'] = $result['privilege'];

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
    public static function loginStatus($needed = 0)
    {

        // Check logged in status.
        if(empty($_SESSION['username'])) {
            return false;
        }

        // Assign authority level.
        switch($_SESSION['privilege']) {
            case 'basic' :
                $authority = 0;
                break;

            case 'moderator':
                $authority = 1;
                break;

            case 'admin':
                $authority = 2;
                break;

            default:
                break;
        }

        // Return whether authority is high enough.
        return ($authority >= $needed);
    }

    /**
     * TODO
     */
    public static function viewUsers()
    {
        if(authorityCheck(1)) {

            $sql = 'SELECT * FROM user';

            $statement = self::$_dbh->prepare($sql);

            $statement->execute();



            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            return $result;


        } else echo 'Invalid request!';
    }
}