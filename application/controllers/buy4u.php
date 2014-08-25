<?php

/**
 * Class Amazon
 * The amazon area
 */
class Buy4u extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * This method controls what happens when you move to /cart/index in your app.
     */
    function index()
    {
        $this->view->title = "| Buy4u Amazon";
        $this->view->description = "Buy4u Amazon";
        $this->view->render('buy4u/index');
    }
}
