<?php

class IsporukaController extends AutorizacijaController
{

    private $viewPutanja = 'privatno'. 
    DIRECTORY_SEPARATOR . 'isporuka' . 
    DIRECTORY_SEPARATOR;
    private $e;
    private $poruka='';
    //private $nf; // number formater dostupan u svim metodama ove klase 
    
    /*public function __construct()
    {
        parent::__construct(); // pozivam parent construct da ode provjerit u autorizacijacontroller dal ima ovlasti
        $this->nf = new NumberFormatter('hr-HR',NumberFormatter::DECIMAL); // format za prikaz broja(radni sat)
        $this->nf->setPattern('###,##0.00');
    }*/

    public function index()
    {
       $isporuka = Isporuka::read();
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
            'podaci' => $isporuka,
            'css' => 'isporuka.css'
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
        if(!$this->kontrolaDatumIsporuke())
        {
            $this->view->render($this->viewPutanja .
            'novi',
            [
                'e'=>$this->e, 
                'poruka'=>$this->poruka
             ]);
             return;
        }
        if(!$this->kontrolaSerijskiKod())
        {
            $this->view->render($this->viewPutanja .
            'novi',
            [
                'e'=>$this->e, 
                'poruka'=>$this->poruka
             ]);
             return;
        }
        if(!$this->kontrolaradnisat())
        {
            $this->view->render($this->viewPutanja .
            'novi',
            [
                'e'=>$this->e, 
                'poruka'=>$this->poruka
             ]);
             return;
        }
        if(!$this->kontrolaimeprezime())
        {
            $this->view->render($this->viewPutanja .
            'novi',
            [
                'e'=>$this->e, 
                'poruka'=>$this->poruka
             ]);
             return;
        }
        if(!$this->kontrolatelefon())
        {
            $this->view->render($this->viewPutanja .
            'novi',
            [
                'e'=>$this->e, 
                'poruka'=>$this->poruka
             ]);
             return;
        }
        if(!$this->kontrolaadresa())
        {
            $this->view->render($this->viewPutanja .
            'novi',
            [
                'e'=>$this->e, 
                'poruka'=>$this->poruka
             ]);
             return;
        }
        //sve ok spremi u bazu
        
        Isporuka::create((array)$this->e);

        $this->view->render($this->viewPutanja . 'novi',
        [
            'e'=>$this->pocetniPodaci(),
            'poruka'=>'Delivery added!'
        ]);
        

       
    }
    //ako nesto ne valja vratiti na view s odgovorom
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
    private function kontrolaDatumIsporuke()
    {

        $s = $this->e->datumIsporuke;
        if(strlen(trim($s))===0)
        {
            $this->poruka='Delivery date is mandatory!';
            return false;
        }

        if(strlen(trim($s)) > 50)
        {
            $this->poruka='Must not have more than 50 characters in Delivery date!';
            return false;
        }

        return true;
    }
    private function kontrolaSerijskiKod()
    {
        $s = $this->e->serijskikod;
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
        $s = $this->e->radnisat;
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
        $e->datumIsporuke='';
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