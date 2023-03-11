<?php

class DjelatnikController extends AdminController
{

    private $viewPutanja = 'privatno'. 
    DIRECTORY_SEPARATOR . 'djelatnik' . 
    DIRECTORY_SEPARATOR;



    public function index()
    {
        $djelatnici = Djelatnik::read();
        foreach($djelatnici as $loz)
        {
            unset($loz->lozinka); //onemogucuje da se lozinka povuce iz baze
        }

        $this->view->render($this->viewPutanja . 'index',
        [
            'podaci'=>$djelatnici
        ]
    );
    }
}