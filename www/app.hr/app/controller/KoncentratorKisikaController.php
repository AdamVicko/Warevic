<?php

class KoncentratorKisikaController extends AutorizacijaController
{
    private $viewPutanja = 'privatno'. 
    DIRECTORY_SEPARATOR . 'koncentratorkisika' . 
    DIRECTORY_SEPARATOR;

    public function index()
    {
        $this->view->render($this->viewPutanja.'index');
    }


}