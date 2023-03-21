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

        Pacijent::create((array)$this->e); //spremi u bazu

        $this->view->render($this->viewPutanja . 'novi',
        [
            'e'=>$this->pocetniPodaci(),
            'poruka'=>'Patient added successfully!'
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
            $this->e = Pacijent::readOne($sifra);
            if($this->e==null)
            {   
                header('location: ' . App::config('url') . 'prijava/odjava' );
                return;
            }
            $this->view->render($this->viewPutanja . 
            'promjena',[
                'e'=>$this->e,
                'poruka'=>'Modify data of Patient!'
            ]);
            return;
        }
        //ako je POST
        $this->e = (object)$_POST; // prebacim post u objekt i posaljem na view koji prima taj objekt Log::info($this->e);
        if(!$this->kontrolaPromjena())//kontrola podataka
            {
                $this->view->render($this->viewPutanja . 
                'promjena',[
                    'e'=>$this->e,
                    'poruka'=>$this->poruka
                ]);
                return;
            }

        $this->e->sifra=$sifra;
        Pacijent::update((array)$this->e);   
        $this->view->render($this->viewPutanja . 
        'promjena',[
            'e'=>$this->e,
            'poruka'=>'Update complete!'
        ]);
    }

    public function izbrisi($sifra=0)
    {
        $sifra=(int)$sifra;
        if($sifra===0)
        {
            header('location: ' . App::config('url') . 'prijava/odjava' );
            return;
        }
        Pacijent::delete($sifra);
        header('location: ' . App::config('url') . 'pacijent/index' );
    }

    private function kontrolaPromjena()
    {
        return $this->kontrolaImeprezime() && $this->kontrolaTelefon() && $this->kontrolaAdresa();
    }

    private function kontrola()
    {
        return $this->kontrolaImeprezime() && $this->kontrolaTelefon() && $this->kontrolaAdresa();
    }

    private function kontrolaAdresa()
    {

        $s = $this->e->adresa;
        if(strlen(trim($s))===0)
        {
            $this->poruka='Patient adress is mandatory!';
            return false;
        }

        if(strlen(trim($s)) > 20)
        {
            $this->poruka='Must not have more than 20 characters in Patient adress!';
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

        if(strlen(trim($s)) > 20)
        {
            $this->poruka='Must not have more than 20 characters in Patient Name and Surname!';
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

        if(strlen(trim($s)) > 30)
        {
            $this->poruka='Must not have more than 30 characters in Patient telephone!';
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