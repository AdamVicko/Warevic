<?php

class PrikupController extends AutorizacijaController
{

    private $viewPutanja = 'privatno'. 
    DIRECTORY_SEPARATOR . 'prikup' . 
    DIRECTORY_SEPARATOR;
    private $e;
    //private $nf; // number formater dostupan u svim metodama ove klase 
    
    /*public function __construct()
    {
        parent::__construct(); // pozivam parent construct da ode provjerit u autorizacijacontroller dal ima ovlasti
        $this->nf = new NumberFormatter('hr-HR',NumberFormatter::DECIMAL); // format za prikaz broja(radni sat)
        $this->nf->setPattern('###,##0.00');
    }*/

    public function index()
    {
        $prikup = Prikup::read();
        /*foreach($prikup as $p)
        {
            if($p->radnisat==null)
            {
                $p->radnisat = $this->nf->format(0);
            }
            else
            {
                $p->radnisat = $this->nf->format($p->radnisat);
            }
        }*/


        $this->view->render($this->viewPutanja . 'index',
        [
            'podaci' => $prikup,
            'css' => 'prikup.css'
        ]);
    }

    public function novi()
    {
        if($_SERVER['REQUEST_METHOD']==='GET')
        {
            $this->view->render($this->viewPutanja . 
            'novi',
            [
                'e'=>$this->pocetniPodaci()
            ]);
            return;
        }
       //ovdje sam siguran da nije GET,za nas je onda POST

       $this->e=(object)$_POST; // prebacim post u objekt i posaljem na view koji prima taj objekt
       $this->view->render($this->viewPutanja . // view postavlje vrijednosti RED
       'novi',
       [
           'e'=>$this->e // ono sto se zapise u input ostane sacuvano
       ]);
       //kontrola podataka i ako je sve ok spremit u bazu 
       //ako nesto ne valja vratiti na view s odgovorom

       
    }

    private function pocetniPodaci()
    {
        $e = new stdClass();
        $e->datumPrikupa='';
        $e->serijskikod='';
        $e->imeprezime='';
        $e->radnisat='';
        $e->adresa='';
        $e->komentar='';
        $e->telefon='';
        return $e;
    }
}