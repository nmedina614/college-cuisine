<?php
require $_SERVER['DOCUMENT_ROOT'] . "/../config/grc_config.php";


class Model
{
    private static $_dbh;
    private static $_user;



    public static function connect() {
        try {
            // instantiate pdo object.
            self::$_dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function login($username, $password) {

        // TODO VALIDATE FUNCTION!!!!!!!

        // Sql statement to be run
        $sql = 'SELECT * FROM `user` WHERE username = :username AND password=:password';

        // Run prepare function
        $statement = self::$_dbh->prepare($sql);

        // Bind parameters.
        $statement->bindParam(':username', $username, PDO::PARAM_STR);
        $statement->bindParam(':password', sha1($password), PDO::PARAM_STR);


        // Execute prepared query
        $statement->execute();

        // If there is a hit, initiate log in.
        if($statement->rowCount() > 0) {
            echo "<p>It worked!</p>";
        } else {
            echo "<p>YOU FAIL!</p>";
        }




    }
}