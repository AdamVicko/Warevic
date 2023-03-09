<?php

class NadzornaplocaController extends Controller
{

    public function index()
    {
        $this->view->render('privatno' . DIRECTORY_SEPARATOR . 
                            'nadzornaPloca');
    }


}