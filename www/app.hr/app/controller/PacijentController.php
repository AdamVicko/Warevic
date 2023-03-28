<?php


class PacijentController 
extends AutorizacijaController 
implements ViewSucelje
{
    private $viewPutanja = 'privatno'. 
    DIRECTORY_SEPARATOR . 'pacijent' . 
    DIRECTORY_SEPARATOR;
    private $e;
    private $poruka='';

    public function dodajpolaznike()
    {
        for($i=0;$i<300;$i++){
            Pacijent::create([
                'imeprezime'=>'Prezime Pacijent' . $i,
                'telefon' =>'',
                'adresa' => '',
                'oib'=>'',
                'pacijentKomentar' => '',
                'datumRodenja' => ''
            ]);
            echo $i . '<br>';
        }

        
    }

    public function index() 
    {
        if(isset($_GET['uvjet']))
        {
            $uvjet = trim($_GET['uvjet']);
        }else
        {
            $uvjet=''; // uvjet za search
        }

        if(isset($_GET['stranica']))
        {
            $stranica = $_GET['stranica'];
        }else
        {
            $stranica=1; // uvjet za search
        }

        $this->view->render($this->viewPutanja . 'index',
        [
            'podaci' => Pacijent::read($uvjet,$stranica),
            'css' => 'pacijent.css',
            'stranica' => $stranica,
            'uvjet' => $uvjet // ucitavam uvjet
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
        //Log::info($sifra);
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
        return $this->kontrolaImeprezime() && $this->kontrolaTelefon() && $this->kontrolaAdresa()
        && $this->kontrolaoib();
    }

    private function kontrola()
    {
        return $this->kontrolaImeprezime() && $this->kontrolaTelefon() && $this->kontrolaAdresa()
        && $this->kontrolaoib();
    }



    private function kontrolaoib() // ovo radi
    {
        $oib=$this->e->oib;
        if (strlen($oib) != 11 || !is_numeric($oib)) {
            $this->poruka='OIB needs to have 11 numbers!';
            return false;
        }
    
        $a = 10;
    
        for ($i = 0; $i < 10; $i++) {
    
            $a += (int)$oib[$i];
            $a %= 10;
    
            if ( $a == 0 ) { $a = 10; }
    
            $a *= 2;
            $a %= 11;
    
        }
    
        $kontrolni = 11 - $a;
    
        if ( $kontrolni == 10 ) { $kontrolni = 0; }
    
        if($kontrolni != intval(substr($oib, 10, 1), 10))
        {
            $this->poruka='OIB is not mathematically correct!';
            return false;
        }
        return true;
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
    public function pocetniPodaci()
    {
        //e kao element
        $e = new stdClass();
        $e->sifra='';
        $e->imeprezime='';
        $e->telefon='';
        $e->datumRodenja='';
        $e->adresa='';
        $e->oib='';
        $e->pacijentKomentar='';
        return $e;
    }

    public function pripremiZaBazu()
    {

    }
    public function pripremiZaView()
    {

    }

}