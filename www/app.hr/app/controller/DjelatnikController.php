<?php

class DjelatnikController extends AdminController
{

    private $viewPutanja = 'privatno'. 
    DIRECTORY_SEPARATOR . 'djelatnik' . 
    DIRECTORY_SEPARATOR;
    private $e;
    private $poruka='';

     public function __construct()
    {
        parent::__construct(); // pozivam parent construct da ode provjerit u autorizacijacontroller dal ima ovlasti
    }

    public function pocetniPodaci()
    {
        //e kao element
        $e = new stdClass();
        $e->sifra='';
        $e->imeprezime='';
        $e->telefon='';
        $e->lozinka='';
        $e->email='';
        $e->uloga='';
        return $e;
    }

    public function index()
    {
        $djelatnik = Djelatnik::read();
        foreach($djelatnik as $loz)
        {
            unset($loz->lozinka); //onemogucuje da se lozinka povuce iz baze
        }
        $poruka='';
    
          if(isset($_GET['uvjet']))
        {
            $uvjet = trim($_GET['uvjet']);
        }else
        {
            $uvjet=''; // uvjet za search
        }
        
        if(isset($_GET['stranica']))
        {
            $stranica = (int)$_GET['stranica'];
            if($stranica < 1)
            { 
                $stranica =1;
            }
        }else
        {
            $stranica=1; // uvjet za search
        }
        $up = Djelatnik::ukupnoDjelatnika($uvjet);
        $zadnja = (int)ceil($up/App::config('brps')); // ceil= zaokruzi na prvi veci cijeli broj ako je decimalno 
        $this->view->render($this->viewPutanja . 'index',
        [
            'podaci'=>$djelatnik,
            'css' => 'djelatnik.css',
            'stranica' => $stranica,
            'uvjet' => $uvjet, // ucitavam uvjet
            'zadnja' => $zadnja,
            'poruka' => $poruka
        ]);
    }

     public function novi()
    {
        if($_SERVER['REQUEST_METHOD']==='GET')
        {
            $this->view->render($this->viewPutanja . 
            'novi',
            [
                'e'=>$this->pocetniPodaci(),
                'poruka'=>$this->poruka
            ]);
            return;
        }
       
        //ovdje sam siguran da nije GET,za nas je onda POST
       
        $this->e = (object)$_POST; // prebacim post u objekt i posaljem na view koji prima taj objekt
        if(!$this->kontrola())
        {
            $this->view->render($this->viewPutanja .
            'novi',
            [
                'e'=>$this->e,
                'poruka'=>$this->poruka
             ]);
             return;
        }

        Djelatnik::create((array)$this->e); //spremi u bazu

        $this->view->render($this->viewPutanja . 'novi',
        [
            'e'=>$this->pocetniPodaci(),
            'poruka'=>'Worker added successfully!'
        ]);
    }

    public function promjena($sifra='')
    {
        if($_SERVER['REQUEST_METHOD']==='GET')
        { 
            if(strlen(trim($sifra))===0)
            {
                header('location: ' . App::config('url') . 'prijava/odjava' );
                return;
            }
            $sifra=(int)$sifra;
            if($sifra===0)
            {

                header('location: ' . App::config('url') . 'prijava/odjava' );
                return;
            }
            $this->e = Djelatnik::readOne($sifra);
            if($this->e==null)
            {   
                header('location: ' . App::config('url') . 'prijava/odjava' );
                return;
            }
            $this->view->render($this->viewPutanja . 
            'promjena',[
                'e'=>$this->e,
                'poruka'=>'Modify data of Worker!'
            ]);
            return;
        }
        //ako je POST
        $this->e = (object)$_POST; // prebacim post u objekt i posaljem na view koji prima taj objekt Log::info($this->e);
        if(!$this->kontrola())//kontrola podataka
            {
                $this->view->render($this->viewPutanja . 
                'promjena',[
                    'e'=>$this->e,
                    'poruka'=>$this->poruka
                ]);
                return;
            }

        $this->e->sifra=$sifra;
        Djelatnik::update((array)$this->e);   
        $this->view->render($this->viewPutanja . 
        'promjena',[
            'e'=>$this->e,
            'poruka'=>'Update complete!'
        ]);
    }

    public function izbrisi($sifra=0)
    {
        
        $sifra=(int)$sifra;
        //Log::info($sifra);
        if($sifra===0)
        {
            header('location: ' . App::config('url') . 'prijava/odjava' );
            return;
        }
        Djelatnik::delete($sifra);
        header('location: ' . App::config('url') . 'djelatnik/index' );
    }
    
    private function kontrola()
    {
        return $this->kontrolaImeprezime();
    }

    private function kontrolaImeprezime()
    {
        $s = $this->e->imeprezime;
        if(strlen(trim($s))===0)
        {
            $this->poruka='Patient Name and Surname are mandatory!';
            return false;
        }

        if(strlen(trim($s)) > 20)
        {
            $this->poruka='Must not have more than 20 characters in Patient Name and Surname!';
            return false;
        }

        return true;
    }

    public function ajaxSearch($uvjet){

        $this->view->api(Djelatnik::read($uvjet));
    }

}