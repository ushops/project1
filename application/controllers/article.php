<?php

/**
 * Class article
 * The article area
 */
class Article extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * This method displays an article according to given id
     */
    function article($id)
    {
		$article_model = $this->loadModel('Article');
        $this->view->article = $article_model->getArticle($id);
		if (!$this->view->article) {
			header('location: ' . URL . 'error');
		}
        $this->view->render('article/article');
    }
}
