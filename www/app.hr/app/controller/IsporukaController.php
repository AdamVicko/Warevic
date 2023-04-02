<?php

class IsporukaController 
extends AutorizacijaController
implements ViewSucelje
{

    private $viewPutanja = 'privatno' . 
    DIRECTORY_SEPARATOR . 'isporuka' . 
    DIRECTORY_SEPARATOR;
    private $e;
    private $poruke=[];



    public function __construct()
    {
        parent::__construct();
       
   }


    public function index()
    {

     $this->view->render($this->viewPutanja . 
            'index',[
                'podaci'=>$this->prilagodiPodatke(Isporuka::read()),
            ]);   
    }

    private function prilagodiPodatke($isporuke)
    {
        foreach($isporuke as $g){
         
            if($g->datumIsporuke==null){
                $g->datumIsporuke = 'Not defined';
            }else{
                $g->datumIsporuke=date('d. m. Y.',strtotime($g->datumpocetka));
            }

        }
        return $isporuke;
    }


    public function novi()
    {
        
       $pacijentSifra = Pacijent::prviPacijent();
       if($pacijentSifra==0){
        header('location: ' . App::config('url') . 'pacijent?p=1');
       }
       $koncentratorKisikaSifra = KoncentratorKisika::prviKoncentrator();
       if($koncentratorKisikaSifra==0){
        header('location: ' . App::config('url') . 'koncentratorKisika?p=1');
       }
       /*
        header('location: ' . 
        App::config('url') . 'grupa/promjena/' .
        Grupa::create([
            'naziv'=>'',
            'smjer'=>$smjerSifra,
            'predavac'=>null,
            'datumpocetka'=>null,
            'maksimalnopolaznika'=>20
        ]));
        */
        $this->promjena(Isporuka::create([
            'datumIsporuke'=>'',
            'pacijent'=>$pacijentSifra,
            'koncentratorKisika'=>$koncentratorKisikaSifra
        ]));
    }

    public function odustani($sifra='')
    {
        $e=Isporuka::readOne($sifra);
        

        if($e->naziv=='' && 
        $e->predavac==null && 
        $e->datumpocetka==null &&
        count($e->polaznici)==0){
            Isporuka::delete($e->sifra);
            
        }
        header('location: ' . App::config('url') . 'grupa');

    }

    public function promjena($sifra='')
    {

        parent::setCSSdependency([
            '<link rel="stylesheet" href="' . App::config('url') . 'public/css/dependency/jquery-ui.css">'
        ]);
        parent::setJSdependency([
            '<script src="' . App::config('url') . 'public/js/dependency/jquery-ui.js"></script>',
            '<script>
                let url=\'' . App::config('url') . '\';
                let grupasifra=' . $sifra . ';
            </script>'
        ]);

        if($_SERVER['REQUEST_METHOD']==='GET'){
            $this->promjena_GET($sifra);
            return;
        }


        $this->e = (object)$_POST;
        $this->e->polaznici=Grupa::polazniciNaGrupi($sifra);
        try {
            $odabraniSmjer = Smjer::readOne($this->e->smjer);
            $this->e->sifra=$sifra;
            $this->kontrola();
            $this->pripremiZaBazu();
            Grupa::update((array)$this->e);
            header('location:' . App::config('url') . 'grupa');
           } catch (\Exception $th) {
            $this->view->render($this->viewPutanja .
            'detalji',[
                'poruke'=>$this->poruke,
                'smjer'=>$odabraniSmjer,
                'predavaci'=>$this->definirajPredavace(),
                'e'=>$this->e
            ]);
           }        

    }

    private function kontrola()
    {
        $s = $this->e->naziv;
        if(strlen(trim($s))===0){
            $this->poruke['naziv']='Naziv obavezno';
            throw new Exception();
        }
    }

    private function promjena_GET($sifra)
    {
        $this->e = Grupa::readOne($sifra);
      

       if($this->e->datumpocetka!=null){
        $this->e->datumpocetka = date('Y-m-d',strtotime($this->e->datumpocetka));
       }
       $this->view->render($this->viewPutanja . 
       'detalji',[
           'e'=>$this->e,
           'smjer'=>Smjer::readOne($this->e->smjer),
           'predavaci'=>$this->definirajPredavace()
       ]); 
    }

   private function definirajPredavace()
   {
    $predavaci = [];
    $p = new stdClass();
    $p->sifra=0;
    $p->ime='Nije';
    $p->prezime='Odabrano';
    $predavaci[]=$p;
    foreach(Predavac::read() as $predavac){
     $predavaci[]=$predavac;
    }
    return $predavaci;
   }


    public function brisanje($sifra=0){
        $sifra=(int)$sifra;
        if($sifra===0){
            header('location: ' . App::config('url') . 'index/odjava');
            return;
        }
        Grupa::delete($sifra);
        header('location: ' . App::config('url') . 'grupa/index');
    }

   
    public function pripremiZaView()
    {
    }

    public function pripremiZaBazu()
    {
        if($this->e->predavac==0){
            $this->e->predavac=null;
        }
        if($this->e->datumpocetka==''){
            $this->e->datumpocetka=null;
        }
   
    }

   

    public function pocetniPodaci()
    {
        $e = new stdClass();
        $e->naziv='';
        $e->smjer=0;
        $e->predavac=0;
        $e->datumpocetka='';
        $e->maksimalnopolaznika=20;
        return $e;
    }

    public function dodajpolaznik()
    {
        //prvo se trebala pozabaciti postoji li u $_GET
        // traženi parametri
        $res = new stdClass();
        if(!Grupa::postojiPolaznikGrupa($_GET['grupa'],
                    $_GET['polaznik'])){
            Grupa::dodajPolaznikGrupa($_GET['grupa'],
                    $_GET['polaznik']);
            $res->error=false;
            $res->description='Uspješno dodano';
                    }else{
                        $res->error=true;
                        $res->description='Polaznik već postoji na grupi';
                    }

                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($res,JSON_NUMERIC_CHECK);
    }

    public function obrisipolaznik()
    {
        //prvo se trebala pozabaciti postoji li u $_GET
        // traženi parametri

        Grupa::obrisiPolaznikGrupa($_GET['grupa'],
                    $_GET['polaznik']);
               
    }
}