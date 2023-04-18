<?php


class IndexController extends AutorizacijaController 
{

    private $viewPutanja = 'privatno' . 
    DIRECTORY_SEPARATOR . 'index' . 
    DIRECTORY_SEPARATOR;

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if(isset($_GET['uvjet']))
        {
            $uvjet = trim($_GET['uvjet']);
        }else
        {
            $uvjet=''; // uvjet za search
        }

        $this->view->render($this->viewPutanja . 
            'index',[
                'css' => 'index.css',
                'uvjet' => $uvjet,
                'podaci' => Index::searchAll($uvjet)
            ]);
    }

}