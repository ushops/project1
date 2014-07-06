<?php

/**
 * Class Cart
 * The cart area
 */
class Cart extends Controller
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
		$this->view->title = "| Cart";
		$this->view->description = "Cart contents";
        $this->view->render('cart/index');
    }
}
