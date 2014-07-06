<?php

/**
 * Class Test
 * The test area
 */
class Test extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * This method controls what happens when you move to /help/index in your app.
     */
    function index()
    {
		$test_model = $this->loadModel('Test');
        $this->view->users = $test_model->getAllUsers();
		$this->view->title = "| Test";
        $this->view->render('test/index');
    }
}
