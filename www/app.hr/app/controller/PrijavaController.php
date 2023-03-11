<?php


class PrijavaController extends Controller // zbog viewa extenda controller
{
    public function autorizacija()
    {
        if(!isset($_POST['email']) || strlen(trim($_POST['email']))===0) // ako email nije upisan a nalazi se u postu
        {
            $this->view->render('prijava', //idi na prijava i posalji sljedece parametre
            [
                'poruka'=>'Enter email!',
                'email'=>''
            ]);
            return;
        }

        if(!isset($_POST['password']) || strlen(trim($_POST['password']))===0) // ako password nije upisan a nalazi se u postu
        {
            $this->view->render('prijava', //idi na prijava i posalji sljedece parametre
            [
                'poruka'=>'Enter correct password!',
                'email'=>$_POST['email']
            ]);
            return;
        }

        //ovdje sam siguran da imam email i lozinku
        $djelatnik = Djelatnik::autoriziraj($_POST['email'], $_POST['password']);

        if($djelatnik==null)
        {
            $this->view->render('prijava',[
                'poruka' =>'Wrong email or password!',
                'email'=> $_POST['email']
            ]);
            return;
        }

        //uspjesno prijavljen
        $_SESSION['auth']=$djelatnik;
        header('location:' . App::config('url') . 
        'index/index');
        

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
}