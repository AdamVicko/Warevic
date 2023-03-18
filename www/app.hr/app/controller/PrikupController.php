<?php

class PrikupController extends AutorizacijaController
{

    private $viewPutanja = 'privatno'. 
    DIRECTORY_SEPARATOR . 'prikup' . 
    DIRECTORY_SEPARATOR;
    private $e;
    private $poruka='';
    private $nf; // number formater dostupan u svim metodama ove klase 
    
    public function __construct()
    {
        parent::__construct(); // pozivam parent construct da ode provjerit u autorizacijacontroller dal ima ovlasti
        $this->nf = new NumberFormatter('hr-HR',NumberFormatter::DECIMAL); // format za prikaz broja(radni sat)
        $this->nf->setPattern('###,##0.00');
    }

    public function index()
    {
       $prikup = Prikup::read();
       foreach($prikup as $p)
        {
            if($p->radniSat==null)
            {
                $p->radniSat = $this->nf->format(0);
            }
            else
            {
                $p->radniSat = $this->nf->format($p->radniSat);
            }
        }


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
                'e'=>$this->pocetniPodaci(),
                'poruka'=>$this->poruka
            ]);
            return;
        }
       
        //ovdje sam siguran da nije GET,za nas je onda POST
       
        $this->e = (object)$_POST; // prebacim post u objekt i posaljem na view koji prima taj objekt
       //Log::info($this->e);
       //Log::info($this->poruka);

       //kontrola podataka
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
        //priprema za bazu
        $this->e->radniSat = $this->nf->parse($this->e->radniSat);

        //sve ok spremi u bazu
        Prikup::create((array)$this->e);

        $this->view->render($this->viewPutanja . 'novi',
        [
            'e'=>$this->pocetniPodaci(),
            'poruka'=>'Collection added!'
        ]);
        

       
    }
    //ako nesto ne valja vratiti na view s odgovorom
    private function kontrola()
    {
        return $this->kontrolaimeprezime() && $this->kontrolaDatumPrikupa() && 
        $this->kontrolaadresa() && $this->kontrolaradnisat() && $this->kontrolaSerijskiKod()
        && $this->kontrolatelefon();
    }
    private function kontrolaimeprezime()
    {

        $s = $this->e->imeprezime;
        if(strlen(trim($s))===0)
        {
            $this->poruka='Patient Name and Surname are mandatory!';
            return false;
        }

        if(strlen(trim($s)) > 50)
        {
            $this->poruka='Must not have more than 50 characters in Patient Name and Surname!';
            return false;
        }

        return true;
    }
    private function kontrolatelefon()
    {

        $s = $this->e->telefon;
        if(strlen(trim($s))===0)
        {
            $this->poruka='Patient telephone is mandatory!';
            return false;
        }

        if(strlen(trim($s)) > 50)
        {
            $this->poruka='Must not have more than 50 characters in Patient telephone!';
            return false;
        }

        return true;
    }
    private function kontrolaadresa()
    {

        $s = $this->e->adresa;
        if(strlen(trim($s))===0)
        {
            $this->poruka='Patient adres is mandatory!';
            return false;
        }

        if(strlen(trim($s)) > 50)
        {
            $this->poruka='Must not have more than 50 characters in Patient adres!';
            return false;
        }

        return true;
    }
    private function kontrolaDatumPrikupa()
    {

        $s = $this->e->datumPrikupa;
        if(strlen(trim($s))===0)
        {
            $this->poruka='Collection date is mandatory!';
            return false;
        }

        if(strlen(trim($s)) > 50)
        {
            $this->poruka='Must not have more than 50 characters in Collection date!';
            return false;
        }

        return true;
    }
    private function kontrolaSerijskiKod()
    {
        $s = $this->e->serijskiKod;
        if(strlen(trim($s))===0)
        {
            $this->poruka='OC Serial number is mandatory!';
            return false;
        }

        if(strlen(trim($s)) > 50)
        {
            $this->poruka='Must not have more than 50 characters in Serial number!';
            return false;
        }

        return true;
    }
    private function kontrolaradnisat()
    {
        $s = $this->nf->parse($this->e->radniSat); //provjera jel u floaatu(double)
        //Log::info($s);
        if(!$s)
        {
            $this->poruka='OC Working hours are not in good format!';
            return false;
        }
        if($s<=0)
        {
            $this->poruka='OC Working hours must be greater than zero!';
            return false;
        }
        if($s>100000)
        {
            $this->poruka='OC Working hours must be lower then one hundred thousand!';
            return false;
        }
        if(strlen(trim($s))===0)
        {
            $this->poruka='OC Working hour is mandatory!';
            return false;
        }

        if(strlen(trim($s)) > 50)
        {
            $this->poruka='Must not have more than 50 characters in Working hour!';
            return false;
        }

        return true;
    }
    private function pocetniPodaci()
    {
        //e kao element
        $e = new stdClass();
        $e->datumPrikupa='';
        $e->serijskiKod='';
        $e->imeprezime='';
        $e->radniSat='';
        $e->adresa='';
        $e->ocKomentar='';
        $e->telefon='';
        $e->pacijentKomentar='';
        $e->datumRodenja='';
        $e->oib='';
        return $e;
    }
}