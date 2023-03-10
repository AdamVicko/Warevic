<?php

abstract class AutorizacijaController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if(!App::auth())
        {
            $this->view->render('prijava',
            [
                'poruka' => 'Log in first',
                'email' => ''
            ]);
        }
    }
}