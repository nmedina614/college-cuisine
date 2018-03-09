<?php

/*
 * This file should contain domain specific login credentials:
 * DB_DSN
 * DB_USERNAME
 * DB_PASSWORD
 */
require $_SERVER['DOCUMENT_ROOT'] . "/../config/cc_config.php";

class Model
{
    private static $_dbh;

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
            $_SESSION['id'] = $result['userid'];
            $_SESSION['username'] = $result['username'];
            $_SESSION['email'] = $result['email'];
            $_SESSION['clearance'] = $result['clearance'];

            return true;

        } else return false;



    }
}