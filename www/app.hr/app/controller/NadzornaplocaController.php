<?php

class NadzornaplocaController extends AutorizacijaController
{

    public function index()
    {
        $this->view->render('privatno' . DIRECTORY_SEPARATOR . 
                            'nadzornaPloca');
    }


}