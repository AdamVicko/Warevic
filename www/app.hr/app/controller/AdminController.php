<?php


abstract class AdminController extends AutorizacijaController
{
    public function __construct()
    {
        parent::__construct();
        if(!App::admin())
        {
            $this->view->render('privatno' . 
            DIRECTORY_SEPARATOR . 'nadzornaPloca');
        }
    }
}