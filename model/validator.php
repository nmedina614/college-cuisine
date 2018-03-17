<?php
/**
 * Created by PhpStorm.
 * User: nash
 * Date: 3/17/2018
 * Time: 1:05 AM
 */

class Validator
{
    /**
     * TODO
     *
     * @returns
     */
    public static function validateRegistration($username, $password1, $password2, $email)
    {
        $invalid = array();


        if(preg_match("/^[0-9a-zA-Z_]{8,}$/", $_POST["user"])) {
            $invalid[] = 'User must be bigger that 8 chars and contain only digits, letters and underscore';
        }

        if(!empty($password1)) {

            if(strlen($password1) <= '8') {

                $invalid[] = "Your Password Must Contain At Least 8 Characters!";
            }
            if(!preg_match("#[0-9]+#",$password1)) {

                $invalid[] = "Your Password Must Contain At Least 1 Number!";
            }
            if(!preg_match("#[A-Z]+#",$password1)) {

                $invalid[] = "Your Password Must Contain At Least 1 Capital Letter!";
            }
            if(!preg_match("#[a-z]+#",$password1)) {

                $invalid[] = "Your Password Must Contain At Least 1 Lowercase Letter!";
            }
            if(!preg_match("/[$@$!%*#?&]+/",$password1)) {

                $invalid[] = "Your Password Must Contain At Least 1 special character!";

            }
        }
        elseif($password1 != $password2) {
            $invalid[] = "Passwords do not match!";
        } else {
            $invalid[] = "Please enter password";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $invalid[] = "Invalid email address!";
        }

        return $invalid;
    }

    public static function test_input($data) {
        return $data;
    }
}