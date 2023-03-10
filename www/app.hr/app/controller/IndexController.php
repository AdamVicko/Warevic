<?php


class IndexController extends Controller //4
{
    public function index() 
    {


        $this->view->render('index');

    }

    public function prijava()//5
    {
        $this->view->render('prijava',
        [
            'poruka'=>'',
            'email'=>''
        ]);
    }

    public function odjava()//on click
    {
        unset($_SESSION['auth']);
        session_destroy();
        header('location:' . App::config('url'));
    }

    public function pacijent()
    {
        $this->view->render('pacijent');
    }

}