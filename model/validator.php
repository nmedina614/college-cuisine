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
     * @param $input
     */
    public static function validUsername($input)
    {
        return preg_match('/^[a-zA-Z0-9]{5,}$/', $input);
    }

    /**
     * TODO
     *
     * @param $input
     */
    public static function validPassword($input)
    {
        $passwordErrors = array();
        if(!empty($input)) {

            if (strlen($_POST["password"]) <= '32') {

                $passwordErrors[] = "Your Password Must Contain At Least 32 Characters!";

            } elseif(!preg_match("#[0-9]+#",$input)) {

                $passwordErrors[] = "Your Password Must Contain At Least 1 Number!";

            } elseif(!preg_match("#[A-Z]+#",$input)) {

                $passwordErrors[] = "Your Password Must Contain At Least 1 Capital Letter!";

            } elseif(!preg_match("#[a-z]+#",$input)) {

                $passwordErrors[] = "Your Password Must Contain At Least 1 Lowercase Letter!";

            } elseif(!preg_match("/[$@$!%*#?&]+/",$input)) {

                $passwordErrors[] = "Your Password Must Contain At Least 1 special character!";

            }
        }
        elseif(!empty($_POST["password"])) {
            $passwordErrors[] = "Please Check You've Entered Or Confirmed Your Password!";
        } else {
            $passwordErrors[] = "Please enter password";
        }

        return $passwordErrors;
    }

    /**
     * TODO
     *
     * @param $input
     */
    public static function validEmail($input)
    {
        return preg_match('/^((https?|ftp)\://((\[?(\d{1,3}\.){3}\d{1,3}\]?)|(([-a-zA-Z0-9]+\.)+[a-zA-Z]{2,4}))(\:\d+)?(/[-a-zA-Z0-9._?,\'+&amp;%$#=~\\]+)*/?)$/', $input);
    }
}