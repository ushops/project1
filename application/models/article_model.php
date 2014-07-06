<?php

/**
 * ArticleModel
 * ...
 */
class ArticleModel
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
    public function getArticle($id)
    {
        return $this->db->queryWithCache("SELECT * FROM usaddress_articles WHERE id = '$id'");
    }

}
