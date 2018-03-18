<?php

/**
 * Class User
 *
 * Class used to store user information. Comes with methods to help ease
 * user related data manipulation and interaction.
 *
 * Requires access to a model class.
 *
 *
 * @author Aaron Melhaff <nash_melhaff@hotmail.com>
 */
class User
{
    private $_userid;
    private $_username;
    private $_email;
    private $_privilege;

    /**
     * Constructor for the User class.
     *
     * @param $userid int representing the users id within the database.
     * @param $username String representing the users account name.
     * @param $email String email representing the address to contact the user with.
     * @param $privilege String representing the users level of authority in the site.
     */
    public function __construct($userid, $username, $email, $privilege)
    {
        $this->_userid = $userid;
        $this->_username = $username;
        $this->_email = $email;
        $this->_privilege = $privilege;
    }

    /**
     * Getter method for the userid field.
     *
     * @return int Returns the user's id as an int.
     */
    public function getUserid()
    {
        return $this->_userid;
    }

    /**
     * Getter method for the username field.
     *
     * @return String Returns the user's username as a String.
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * Getter method for the email field.
     *
     * @return String Returns the user's email as a String.
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * Getter method for the privilege field.
     *
     * @return String Returns the user's privilege level as a String.
     */
    public function getPrivilege()
    {
        return $this->_privilege;
    }

    /**
     * Method that resets password and sends new one to user.
     *
     * Method that changes the users password to a string of
     * random characters 32 chars in length. Once finished,
     * sends an email to the user with their new random
     * password.
     */
    public function resetPassword()
    {
        // Generate a password from random characters
        $newPassword = Model::generatePassword(32);

        // Update password in database.
        Model::updatePassword($this->getUserid(), $newPassword);

        // Information sent in email.
        $subject = "Password Reset";
        $message = "Your new password is $newPassword";

        // Send email to user email containing password.
        Model::sendMessage($this->getEmail(), $subject, $message);
    }

    /**
     * Method that changes the users password to the input parameter.
     *
     * @param $newPassword String representing the new password.
     */
    public function changePassword($oldPassword, $newPassword1, $newPassword2)
    {
        // Confirm that user knows original password.
        $confirmed = Model::confirmPassword($oldPassword);
        if($confirmed) {
            // Ensure new password is viable.
            $invalid = Validator::validatePassword($newPassword1, $newPassword2);

            // If no errors are found, change password.
            if(count($invalid) === 0) {

                // If everything is valid, change users password.
                Model::updatePassword($this->getUserid(), $newPassword1);

                // Information sent in email.
                $subject = "Password Change";
                $message = "Your password has been updated!
                    If this change was not made by you, contact an administrator!";

                // Send email to user email containing password.
                Model::sendMessage($this->getEmail(), $subject, $message);
            }
        }
        else return array('Old password didn\'t match.');


    }

}