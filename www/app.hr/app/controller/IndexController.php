<?php


class IndexController extends Controller
{
    public function index()
    {


        $this->view->render('index');

    }

    public function prijava()
    {
        $this->view->render('prijava',
        [
            'poruka'=>'',
            'email'=>''
        ]);
    }

    public function pacijent()
    {
        $this->view->render('pacijent');
    }

}