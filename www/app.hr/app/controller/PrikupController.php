<?php

class PrikupController 
extends AutorizacijaController
implements ViewSucelje
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
        $this->nf->setPattern('###.##0.00');
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
            $this->view->render($this->viewPutanja . 'detalji', 
            [
                'legend'=>'New Collection',
                'akcija'=>'Create',
                'poruka'=>'Fullfil needed data!',
                'e'=>$this->pocetniPodaci()
            ]);
            return;
        }
        $this->pripremiZaView();//i da je metoda prazna slobodno ju mogu pozvat!!
        //ovdje sam siguran da nije GET,za nas je onda POST
        

        try {
            $this->kontrola();
            $this->pripremiZaBazu();
            Prikup::create((array)$this->e);//sve ok spremi u bazu
            header('location:' . App::config('url') . 'prikup/index');
        } catch (\Exception $th) {
                $this->view->render($this->viewPutanja . 'detalji' , 
            [
                'legend'=>'New Collection',
                'akcija'=>'Create',
                'poruka'=>$this->poruka,
                'e'=>$this->e
            ]);
        }
       
       //Log::info($this->e);
       //Log::info($this->poruka);

       //kontrola podataka
       /*if(!$this->kontrola())
        {
            $this->view->render($this->viewPutanja .
            'detalji',
            [
                'legend'=>'New Collection',
                'akcija'=>'Dodaj',
                'poruka'=>'Popunite trazene podatke',
                'e'=>$this->e
             ]);
             return;
        }*/
      
       

        
       

      /*  $this->view->render($this->viewPutanja . 'novi',
        [
            'e'=>$this->pocetniPodaci(),
            'poruka'=>'Collection added!'
        ]);*/
    }

    public function promjena($sifra=0)
    {
        if($_SERVER['REQUEST_METHOD']==='GET'){
             $this->e = Prikup::readOne($sifra);
             $this->provjeraIntParametra($sifra);
             if($this->e==null){
                 header('location: ' . App::config('url') . 'prijava/odjava');
                 return;
             }
 
             $this->view->render($this->viewPutanja .
             'detalji',[
                 'legend'=>'Modify collection',
                 'akcija'=>'Modify',
                 'poruka'=>'Modify data',
                 'e'=>$this->e
             ]);
             return;
         }
         //sada ide POST
 
         $this->pripremiZaView();
            
            try {
             $this->e->sifra=$sifra; // POSTO NISAM NA POCETNIM PARAMETRIMA STAVIO SIFRU OVDJE JU DEKLARIRAM
             $this->kontrola();
             $this->pripremiZaBazu();
             Prikup::update((array)$this->e);
             header('location:' . App::config('url') . 'prikup');
            } catch (\Exception $th) {
             $this->view->render($this->viewPutanja .
             'detalji',[
                 'legend'=>'Collection modify error!',
                 'akcija'=>'Modify',
                 'poruka'=>$this->poruka . ' ' . $th->getMessage(),
                 'e'=>$this->e
             ]);
            }
    }

    public function izbrisi($sifra=0)
    {
        $sifra=(int)$sifra;
        if($sifra===0)
        {
            header('location: ' . App::config('url') . 'prijava/odjava' );
            return;
        }
        Prikup::delete($sifra);
        header('location: ' . App::config('url') . 'prikup/index' );
    }


    //ako nesto ne valja vratiti na view s odgovorom
    private function kontrola()
    {
        $this->kontrolaimeprezime();
        $this->kontrolaDatumPrikupa();
        $this->kontrolaadresa();
        $this->kontrolaradnisat();
        $this->kontrolaSerijskiKod();
        $this->kontrolatelefon();
        $this->kontrolaoib();
    }

    private function kontrolaimeprezime()
    {

        $s = $this->e->imeprezime;
        if(strlen(trim($s))===0)
        {
            $this->poruka='Patient Name and Surname are mandatory!';
            throw new Exception();
        }

        if(strlen(trim($s)) > 50)
        {
            $this->poruka='Must not have more than 50 characters in Patient Name and Surname!';
            throw new Exception();
        }
    }
    private function kontrolatelefon()
    {

        $s = $this->e->telefon;
        if(strlen(trim($s))===0)
        {
            $this->poruka='Patient telephone is mandatory!';
            throw new Exception();
        }

        if(strlen(trim($s)) > 50)
        {
            $this->poruka='Must not have more than 50 characters in Patient telephone!';
            throw new Exception();
        }
    }
    private function kontrolaadresa()
    {

        $s = $this->e->adresa;
        if(strlen(trim($s))===0)
        {
            $this->poruka='Patient adres is mandatory!';
            throw new Exception();
        }

        if(strlen(trim($s)) > 50)
        {
            $this->poruka='Must not have more than 50 characters in Patient adres!';
            throw new Exception();
        }
    }
    private function kontrolaDatumPrikupa()
    {

        $s = $this->e->datumPrikupa;
        if(strlen(trim($s))===0)
        {
            $this->poruka='Collection date is mandatory!';
            throw new Exception();
        }

        if(strlen(trim($s)) > 50)
        {
            $this->poruka='Must not have more than 50 characters in Collection date!';
            throw new Exception();
        }
    }
    private function kontrolaoib()
    {
        $oib=$this->e->oib;
        if (strlen($oib) != 11 || !is_numeric($oib)) {
            $this->poruka='OIB needs to have 11 numbers!';
            throw new Exception();
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
            throw new Exception();
        }
    }
    
    private function kontrolaSerijskiKod()
    {
        $s = $this->e->serijskiKod;
        if(strlen(trim($s))===0)
        {
            $this->poruka='OC Serial number is mandatory!';
            throw new Exception();
        }

        if(strlen(trim($s)) > 50)
        {
            $this->poruka='Must not have more than 50 characters in Serial number!';
            throw new Exception();
        }
    }
    private function kontrolaradnisat()
    {
        $s = $this->nf->parse($this->e->radniSat); //provjera jel u floaatu(double)
        //Log::info($s);
        if(!$s)
        {
            $this->poruka='OC Working hours are not in good format!';
            throw new Exception();
        }
        if($s<=0)
        {
            $this->poruka='OC Working hours must be greater than zero!';
            throw new Exception();
        }
        if($s>100000)
        {
            $this->poruka='OC Working hours must be lower then one hundred thousand!';
            throw new Exception();
        }
        if(strlen(trim($s))===0)
        {
            $this->poruka='OC Working hour is mandatory!';
            throw new Exception();
        }

        if(strlen(trim($s)) > 50)
        {
            $this->poruka='Must not have more than 50 characters in Working hour!';
            throw new Exception();
        }
    }

    public function pripremiZaBazu()
    {
        $this->e->radniSat = $this->nf->parse($this->e->radniSat);  //priprema za bazu
    }

    public function pripremiZaView()
    {
        $this->e = (object)$_POST; // prebacim post u objekt i posaljem na view koji prima taj objekt
    }

    public function pocetniPodaci()
    {
        //e kao element
        $e = new stdClass();
        $e->sifra='';
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