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

    public function search($uvjet)
    {
        $rez =Index::searchAll($uvjet);
        usort($rez, function($a, $b) { return strcmp($a->tekst, $b->tekst); });
        $this->view->api($rez);
    }

    public function index()
    {

        $this->view->render($this->viewPutanja . 
            'index',[
                'css' => 'index.css',
            ]);
            
    }

}