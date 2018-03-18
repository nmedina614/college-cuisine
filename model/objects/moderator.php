<?php
/**
 * Class Moderator
 *
 * Class used to extend the types of data manipulation available.
 * Comes with methods to help ease user related data manipulation
 * and interaction.
 *
 * Requires access to a model class.
 *
 * @author Aaron Melhaff <nash_melhaff@hotmail.com>
 */
class Moderator extends User
{

    /**
     * Method for disabling another user based on id.
     *
     * @param $userid int Id of user being banned.
     */
    public function ban($userid)
    {
        Model::reassignUser($userid, 'deactivated');
    }

    /**
     * Method for reinstating another user based on id.
     *
     * @param $userid int Id of user being reinstated.
     */
    public function reinstate($userid)
    {
        Model::reassignUser($userid, 'basic');
    }

    /**
     * Method for promoting another user based on id.
     *
     * @param $userid int Id of user being promoted.
     */
    public function promote($userid)
    {
        Model::reassignUser($userid, 'moderator');
    }

    /**
     * Method for demoting another user based on id.
     *
     * @param $userid int Id of user being demoted.
     */
    public function demote($userid)
    {
        Model::reassignUser($userid, 'basic');
    }

    /**
     * Method for deleting another user based on id.
     *
     * @param $userid int Id of user being deleted.
     */
    public function delete($userid)
    {
        Model::deleteUser($userid);
    }

}