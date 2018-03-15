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
     *
     */
    public function changePassword()
    {

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


}