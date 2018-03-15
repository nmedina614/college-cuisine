<?php

/**
 * Class User TODO
 */
class User
{
    private $_userid;
    private $_username;
    private $_email;
    private $_privilege;

    /**
     * User constructor. TODO
     * @param $userid
     * @param $username
     * @param $password
     * @param $email
     * @param $privilege
     */
    public function __construct($userid, $username, $email, $privilege)
    {
        $this->_userid = $userid;
        $this->_username = $username;
        $this->_email = $email;
        $this->_privilege = $privilege;
    }

    /**
     * @return mixed
     */
    public function getUserid()
    {
        return $this->_userid;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * @return mixed
     */
    public function getPrivilege()
    {
        return $this->_privilege;
    }

    /**
     * TODO
     *
     * @param $userid
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
     * TODO
     *
     * @param $newPassword
     */
    public function changePassword($newPassword)
    {
        Model::updatePassword($this->getUserid(), $newPassword);

        // Information sent in email.
        $subject = "Password Change";
        $message = "Your password has been updated!
            If this change was not made by you, contact an administrator!";

        // Send email to user email containing password.
        Model::sendMessage($this->getEmail(), $subject, $message);
    }


}