<?php

class KoncentratorKisikaController extends AutorizacijaController
{
    private $viewPutanja = 'privatno'. 
    DIRECTORY_SEPARATOR . 'koncentratorKisika' . 
    DIRECTORY_SEPARATOR;
    private $e;
    private $poruka='';

    public function index()
    {
        $koncentratorKisika = KoncentratorKisika::read();
        $this->view->render($this->viewPutanja.'index',
        [
            'podaci' => $koncentratorKisika,
            'css' => 'koncentratorKisika.css'
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

        KoncentratorKisika::create((array)$this->e);

        $this->view->render($this->viewPutanja . 'novi',
        [
            'e'=>$this->pocetniPodaci(),
            'poruka'=>'Oxygen Concentrator added successfully!'
        ]);
    }
    private function kontrola()
    {
        return $this->kontrolaModel() && $this->kontrolaDatumKupovine() && 
        $this->kontrolaRadniSat() && $this->kontrolaSerijskiKod();
    }

    private function kontrolaSerijskiKod()
    {

        $s = $this->e->serijskiKod;
        if(strlen(trim($s))===0)
        {
            $this->poruka='Serial number is mandatory!';
            return false;
        }

        if(strlen(trim($s)) > 50)
        {
            $this->poruka='Must not have more than 50 characters in Serial number!';
            return false;
        }

        return true;
    }
    private function kontrolaRadniSat()
    {

        $s = $this->e->radniSat;
        if(strlen(trim($s))===0)
        {
            $this->poruka='Working hours are mandatory!';
            return false;
        }

        if(strlen(trim($s)) > 50)
        {
            $this->poruka='Must not have more than 50 characters in Working hours!';
            return false;
        }

        return true;
    }
    private function kontrolaModel()
    {
        $s = $this->e->model;
        if(strlen(trim($s))===0)
        {
            $this->poruka='Model is mandatory!';
            return false;
        }

        if(strlen(trim($s)) > 50)
        {
            $this->poruka='Must not have more than 50 characters in Model!';
            return false;
        }

        return true;
    }
    private function kontrolaDatumKupovine()
    {

        $s = $this->e->datumKupovine;
        if(strlen(trim($s))===0)
        {
            $this->poruka='Date of buying is mandatory!';
            return false;
        }

        if(strlen(trim($s)) > 50)
        {
            $this->poruka='Must not have more than 50 characters in Date of buying!';
            return false;
        }

        return true;
    }
    private function pocetniPodaci()
    {
        //e kao element
        $e = new stdClass();
        $e->serijskiKod='';
        $e->radniSat='';
        $e->proizvodac='';
        $e->model='';
        $e->ocKomentar='';
        $e->datumKupovine='';
        return $e;
    }
}