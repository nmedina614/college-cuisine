<?php
/**
 * Author: Aaron Melhaff
 * Date: 3/15/2018
 * Time: 1:51 PM
 */

class Moderator extends User
{

    /**
     * TODO
     *
     * @param $userid
     */
    public function ban($userid)
    {
        Model::reassignUser($userid, 'deactivated');
    }

    /**
     * TODO
     *
     * @param $userid
     */
    public function reinstate($userid)
    {
        Model::reassignUser($userid, 'basic');
    }

    /**
     * TODO
     *
     * @param $userid
     */
    public function promote($userid)
    {
        Model::reassignUser($userid, 'moderator');
    }

    /**
     * TODO
     *
     * @param $userid
     */
    public function demote($userid)
    {
        Model::reassignUser($userid, 'basic');
    }

}