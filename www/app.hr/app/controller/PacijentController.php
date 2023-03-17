<?php


class PacijentController extends AutorizacijaController 
{
    private $viewPutanja = 'privatno'. 
    DIRECTORY_SEPARATOR . 'pacijent' . 
    DIRECTORY_SEPARATOR;
    private $e;
    private $poruka='';


    public function index() 
    {
        $pacijent = Pacijent::read();
        $this->view->render($this->viewPutanja . 'index',
        [
            'podaci' => $pacijent,
            'css' => 'pacijent.css'
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

        Pacijent::create((array)$this->e);

        $this->view->render($this->viewPutanja . 'novi',
        [
            'e'=>$this->pocetniPodaci(),
            'poruka'=>'Patient added successfully!'
        ]);
    }
    private function kontrola()
    {
        return $this->kontrolaImeprezime() && $this->kontrolaTelefon() && 
        $this->kontrolaDatumrodenja() && $this->kontrolaAdresa();
    }

    private function kontrolaAdresa()
    {

        $s = $this->e->adresa;
        if(strlen(trim($s))===0)
        {
            $this->poruka='Patient adress is mandatory!';
            return false;
        }

        if(strlen(trim($s)) > 50)
        {
            $this->poruka='Must not have more than 50 characters in Patient adres!';
            return false;
        }

        return true;
    }
    private function kontrolaDatumRodenja()
    {

        $s = $this->e->datumRodenja;
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
    private function kontrolaImeprezime()
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
    private function kontrolaTelefon()
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
    private function pocetniPodaci()
    {
        //e kao element
        $e = new stdClass();
        $e->imeprezime='';
        $e->telefon='';
        $e->datumRodenja='';
        $e->adresa='';
        $e->oib='';
        $e->pacijentKomentar='';
        return $e;
    }

}