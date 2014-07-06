<?php

/**
 * OverviewModel
 * Handles data for overviews (pages that show user profiles / lists)
 */
class TestModel
{
    /**
     * Constructor, expects a Database connection
     * @param Database $db The Database object
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Gets an array that contains all the users in the database. The array's keys are the user ids.
     * Each array element is an object, containing a specific user's data.
     * @return array The profiles of all users
     */
    public function getAllUsers()
    {
        return $this->db->queryWithCache("SELECT user_id, user_name, user_email, user_active, user_has_avatar FROM usaddress_users ORDER BY user_id ASC");
    }

}
