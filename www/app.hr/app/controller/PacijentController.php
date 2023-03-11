<?php


class PacijentController extends AutorizacijaController 
{
    public function index() 
    {

        $this->view->render('privatno' . DIRECTORY_SEPARATOR . 
        'pacijent');

    }
}