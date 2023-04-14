<?php

class PrikupController 
extends AutorizacijaController
implements ViewSucelje
{

    private $viewPutanja = 'privatno'. 
    DIRECTORY_SEPARATOR . 'prikup' . 
    DIRECTORY_SEPARATOR;
    private $e;
    
    public function __construct()
    {
        parent::__construct(); // pozivam parent construct da ode provjerit u autorizacijacontroller dal ima ovlasti
    }

    public function pocetniPodaci()
    {
        $e = new stdClass();
        $e->datumPrikupa='';
        $e->imeprezime='';
        $e->serijskiKod='';
        return $e;
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
            $stranica = (int)$_GET['stranica'];
            if($stranica < 1)
            { 
                $stranica =1;
            }
        }else
        {
            $stranica=1; // uvjet za search
        }
        $ui = Prikup::ukupnoPrikupa($uvjet);
        $zadnja = (int)ceil($ui/App::config('brps')); // ceil= zaokruzi na prvi veci cijeli broj ako je decimalno
        $this->view->render($this->viewPutanja . 
            'index',[
                'podaci'=>Prikup::read($uvjet,$stranica),
                'css' => 'prikup.css',
                'stranica' => $stranica,
                'uvjet' => $uvjet, // ucitavam uvjet
                'zadnja' => $zadnja
            ]);
    }

    public function novi()
    {
        $pacijenti=Pacijent::read();
        $koncentratoriKisika=KoncentratorKisika::read();
        $this->view->render($this->viewPutanja . 
            'detaljiNovo',[
            'e'=>$this->e,
            'pacijenti'=>$pacijenti,
            'koncentratoriKisika' => $koncentratoriKisika
       ]); // kreiram odma isporuku kako bi ju mogo napunit s pacijentom i koncentratorom kisika
    }

    public function noviPrikup()
    {
        Prikup::noviPrikup();
        $this->index();
    }

    public function azurirajPrikup($sifra)
    {
        Prikup::azurirajPrikup($sifra);
        $this->index();
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

    public function odustani($sifra='')
    {
        $e=Prikup::readOne($sifra);
        if(empty($e->pacijentSifra)&&(empty($e->kisikSifra)))
        {
            Prikup::delete($e->sifra);
        }
        header('location: ' . App::config('url') . 'prikup/index');
    }

    public function promjena($sifra='')
    {
        parent::setCSSdependency([
            '    <link rel="stylesheet" href="' . App::config('url') . 'public/css/dependency/jquery-ui.css">'
        ]);
        parent::setJSdependency([
            '<script src="' . App::config('url') . 'public/js/dependency/jquery-ui.js"></script>',
            '<script>
                let url=\'' . App::config('url') . '\';
                let isporukasifra=' . $sifra . ';
            </script>' // app config ima zapisano na kojem smo url-u, iz php proslijedujem controlleru da postoji varijabla URL nakon koje dolazi JS
        ]);
        $pacijenti=Pacijent::read();
        $koncentratoriKisika=KoncentratorKisika::read();
        $trenutniPodaciPrikupa = Prikup::readOne($sifra);
        $this->view->render($this->viewPutanja . 
            'detalji',[
            'pacijenti'=> $pacijenti,
            'koncentratoriKisika' => $koncentratoriKisika,
            'trenutniPodaciPrikupa' => $trenutniPodaciPrikupa
       ]);
    }

    public function obrisipacijent($sifra)
    {

        Prikup::obrisiPacijentPrikup($sifra);
        
    }
    public function obrisiKoncentratorKisika()
    {
        Prikup::obrisiKoncentratorKisikaPrikup($_GET['isporuka'],
            $_GET['kisikSifra']);
    }

  

    //ako nesto ne valja vratiti na view s odgovorom
  /*  private function kontrola()
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
 */
    public function pripremiZaBazu()
    {
    }

    public function pripremiZaView()
    {
        $this->e = (object)$_POST; // prebacim post u objekt i posaljem na view koji prima taj objekt
    }

 

    
}