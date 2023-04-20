<?php

class KoncentratorKisikaController extends AutorizacijaController
{
    private $viewPutanja = 'privatno'. 
    DIRECTORY_SEPARATOR . 'koncentratorKisika' . 
    DIRECTORY_SEPARATOR;
    private $e;
    private $poruka='';

    
    public function __construct()
    {
        parent::__construct(); // pozivam parent construct da ode provjerit u autorizacijacontroller dal ima ovlasti

    }
    public function index()
    {
        $poruka='';
        if(isset($_GET['p']))
        {
            switch ((int)$_GET['p']) {//stavljamo int ispred jer podatak dolazi sa http-a kao string!
                case 2:
                    $poruka=' To add delivery first you need to create Oxygen Concentrator!';
                    break;
                
                default:
                    $poruka='';
                    break;
            }
        }
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
        $uk = KoncentratorKisika::ukupnoKisika($uvjet);
        $zadnja = (int)ceil($uk/App::config('brps')); // ceil= zaokruzi na prvi veci cijeli broj ako je decimalno
        $koncentratorKisika = KoncentratorKisika::read();


        $this->view->render($this->viewPutanja.'index',
        [
            'podaci' => KoncentratorKisika::read($uvjet,$stranica),
            'css' => 'koncentratorKisika.css',
            'poruka' => $poruka,
            'stranica' => $stranica,
            'uvjet' => $uvjet, // ucitavam uvjet
            'zadnja' => $zadnja
        ]);
    }
    public function novi()
    {
        if($_SERVER['REQUEST_METHOD']==='GET')
        {
            $this->pozoviView(
                [
                    'e'=>$this->pocetniPodaci(),
                    'poruka'=>$this->poruka
                ]
            );
        }//ovdje sam siguran da nije GET,za nas je onda POST
        $this->e = (object)$_POST; // prebacim post u objekt i posaljem na view koji prima taj objekt Log::info($this->e);
        if(!$this->kontrolaNovi())//kontrola podataka
            {
                $this->pozoviView(//ako nesto nevalja vrati poruku
                    [
                        'e'=>$this->e, 
                        'poruka'=>$this->poruka
                    ]
                );
                return;
            }

            KoncentratorKisika::create((array)$this->e); //ako je sve u redu spremaj u bazu
            $this->pozoviView(
                [
                    'e'=>$this->pocetniPodaci(),
                    'poruka'=>'Oxygen Concentrator added successfully!'
                ]
            );
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
            $this->e = KoncentratorKisika::readOne($sifra);
            if($this->e==null)
            {   
                header('location: ' . App::config('url') . 'prijava/odjava' );
                return;
            }

            $this->view->render($this->viewPutanja . 
            'promjena',[
                'e'=>$this->e,
                'poruka'=>'Modify data of Oxygen Concentrator!'
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
        KoncentratorKisika::update((array)$this->e);   
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
        KoncentratorKisika::delete($sifra);
        header('location: ' . App::config('url') . 'koncentratorKisika/index' );
    }
    
    private function pozoviView($parametri)
    {
        $this->view->render($this->viewPutanja . 
        'novi',$parametri);
    }

    private function kontrolaNovi() // razdavajome kontrole za noi i promjenu zbog mogucnosti da neke stvari ne zelimo provjeravat
    {
        return $this->kontrolaSerijskiKod() && $this->kontrolaRadniSat() && 
        $this->kontrolaDatumKupovine() && $this->kontrolaModel();
    }
    private function kontrolaPromjena()
    {
        return $this->kontrolaSerijskiKod() && $this->kontrolaRadniSat() && 
        $this->kontrolaDatumKupovine() && $this->kontrolaModel();
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

        if(KoncentratorKisika::postojiIstiUBazi($s))
        {
            $this->poruka='The same Serial number is already in database!';
            return false;
        }

        return true;
    }
    private function kontrolaRadniSat()
    {
        $s =$this->e->radniSat; 
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

        if(strlen(trim($s)) > 20)
        {
            $this->poruka='Must not have more than 20 characters in Model!';
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
        $e->sifra='';
        $e->serijskiKod='';
        $e->radniSat='';
        $e->proizvodac='';
        $e->model='';
        $e->ocKomentar='';
        $e->datumKupovine='';
        return $e;
    }

    public function ajaxSearch($uvjet){

        /* $pacijenti=Pacijent::read($uvjet);
 
 
         foreach($pacijenti as $p){
             if(file_exists(BP . 'public' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR
             . 'polaznici' . DIRECTORY_SEPARATOR . $p->sifra . '.png' )){
                 $p->slika= App::config('url') . 'public/img/polaznici/' . $p->sifra . '.png';
             }else{
                 $p->slika= App::config('url') . 'public/img/nepoznato.png';
             }
         }
         $this->view->api($pacijenti)
 */
 
         $this->view->api(KoncentratorKisika::read($uvjet));
     }
}