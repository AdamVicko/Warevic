<?php

class KoncentratorKisikaController extends AutorizacijaController
{

    public function index()
    {
        $this->view->render('privatno' . DIRECTORY_SEPARATOR . 
                            'koncentratorKisika');
    }


}