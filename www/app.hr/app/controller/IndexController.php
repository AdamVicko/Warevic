<?php


class IndexController extends AutorizacijaController //4
{
    public function index() 
    {


        $this->view->render('privatno' . DIRECTORY_SEPARATOR . 
        'index');

    }

}