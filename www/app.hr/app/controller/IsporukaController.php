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
                'podaci'=>Isporuka::read()
            ]);   
    }

    public function novi()
    {
        $pacijentSifra = Pacijent::prviPacijent();
        if($pacijentSifra==0){
         header('location: ' . App::config('url') . 'pacijent/index');
        }

        $koncentratorSifra = KoncentratorKisika::prviKoncentrator();
        if($koncentratorSifra==0){
         header('location: ' . App::config('url') . 'koncentratorKisika/index');
        }
        
       /*
        header('location: ' . 
        App::config('url') . 'Isporuka/promjena/' .
        Isporuka::create([
            'datumIsporuke'=>'',
            'pacijent'=>null,
            'koncentratorKisika'=>null
        ]));
        */
        $this->promjena(Isporuka::create([
            'datumIsporuke'=>'',
            'pacijent'=>$pacijentSifra,
            'koncentratorKisika'=>$koncentratorSifra
        ])); ///////////////////////////// tru si stao provjeri jel ti to radi bar da ucita kao i njegovo na videu zatim ide promjena!!!!!!!
    }

    public function odustani($sifra='')
    {
        $e=Isporuka::readOne($sifra);
        

        if($e->datumpocetka=='' &&
        $e->pacijent==null && 
        $e->koncentratorKisika==null)
        {
            Isporuka::delete($e->sifra);
        }
        header('location: ' . App::config('url') . 'isporuka/index');

    }

    public function promjena($sifra='')
    {

        if($_SERVER['REQUEST_METHOD']==='GET'){
            $this->promjena_GET($sifra);
            return;
        }

        $this->e = (object)$_POST;
        $this->e->pacijent=Isporuka::pacijentNaIsporuki($sifra);
        $this->e->koncentratorKisika=Isporuka::koncentratorKisikaNaIsporuki($sifra);
        try {
            $this->e->sifra=$sifra;
            $this->kontrola();
            $this->pripremiZaBazu();
            Isporuka::update((array)$this->e);
            header('location:' . App::config('url') . 'isporuka/index');
           } catch (\Exception $th) {
            $this->view->render($this->viewPutanja .
            'detalji',[
                'poruke'=>$this->poruke,
                'pacijent'=>$this->definirajPacijenta(),
                'koncentratorKisika'=>$this->definirajKoncentratorKisika(),
                'e'=>$this->e
            ]);
           }        
    }

    private function kontrola()
    {
        $s = $this->e->datumIsporuke;
        if(strlen(trim($s))===0){
            $this->poruke['datumIsporuke']='Delivery date is mandatory!';
            throw new Exception();
        }
    }
    private function promjena_GET($sifra)
    {
        $this->e = Isporuka::readOne($sifra);
      
       if($this->e->datumIsporuke!=null){
        $this->e->datumIsporuke = date('Y-m-d',strtotime($this->e->datumIsporuke));
       }
       $this->view->render($this->viewPutanja . 
       'detalji',[
           'e'=>$this->e,
           'pacijent'=>Pacijent::readOne($this->e->pacijent),
           'koncentratorKisika'=>KoncentratorKisika::readOne($this->e->koncentratorKisika)
       ]); 
    }

    private function definirajKoncentratorKisika()
    {
     $koncentratori = [];
     $p = new stdClass();
     $p->sifra=0;
     $p->serijskiKod='Not defined!';
     $koncentratori[]=$p;
     foreach(KoncentratorKisika::read() as $koncentrator){
      $koncentratori[]=$koncentrator;
     }
     return $koncentratori;
    }

    private function definirajPacijenta()
    {
     $pacijenti = [];
     $p = new stdClass();
     $p->sifra=0;
     $p->imeprezime='Not defined!';
     $pacijenti[]=$p;
     foreach(Pacijent::read() as $pacijent){
      $pacijenti[]=$pacijent;
     }
     return $pacijenti;
    }

    public function izbrisi($sifra=0){
        $sifra=(int)$sifra;
        if($sifra===0){
            header('location: ' . App::config('url') . 'prijava/odjava');
            return;
        }
        Isporuka::delete($sifra);
        header('location: ' . App::config('url') . 'isporuka/index');
    }

   
    public function pripremiZaView()
    {
    }

    public function pripremiZaBazu()
    {

        if($this->e->imeprezime==''){
            $this->e->imeprezime=null;
        }
        if($this->e->serijskiKod==0){
            $this->e->serijskiKod=null;
        }
        if($this->e->datumIsporuke==''){
            $this->e->datumIsporuke=null;
        }
   
    }

   

    public function pocetniPodaci()
    {
        $e = new stdClass();
        $e->datumIsporuke='';
        $e->imeprezime=0;
        $e->serijskiKod=0;
        return $e;
    }

    public function dodajKoncentratorKisika()
    {
        //prvo se trebala pozabaciti postoji li u $_GET
        // traženi parametri
        $res = new stdClass();
        if(!Isporuka::postojiKoncentratorKisikaIsporuka($_GET['Isporuka'],
                    $_GET['koncentratorKisika'])){
                        Isporuka::postojiKoncentratorKisikaIsporuka($_GET['Isporuka'],
                    $_GET['koncentratorKisika']);
            $res->error=false;
            $res->description='Added succesfully!';
                    }else{
                        $res->error=true;
                        $res->description='That is already selected Oxygen Concentrator!';
                    }

                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($res,JSON_NUMERIC_CHECK);
    }

    public function dodajPacijenta()
    {
        //prvo se trebala pozabaciti postoji li u $_GET
        // traženi parametri
        $res = new stdClass();
        if(!Isporuka::postojiPacijentIsporuka($_GET['Isporuka'],
                    $_GET['pacijent'])){
                        Isporuka::postojiPacijentIsporuka($_GET['Isporuka'],
                    $_GET['pacijent']);
            $res->error=false;
            $res->description='Added succesfully!';
                    }else{
                        $res->error=true;
                        $res->description='That is already selected patient!';
                    }

                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($res,JSON_NUMERIC_CHECK);
    }

    public function obrisipacijent()
    {
        //prvo se trebala pozabaciti postoji li u $_GET
        // traženi parametri

        Isporuka::obrisiPacijentIsporuka($_GET['isporuka'],
                    $_GET['pacijent']);
               
    }

    public function obrisiKoncentratorKisika()
    {
        //prvo se trebala pozabaciti postoji li u $_GET
        // traženi parametri

        Isporuka::obrisiPacijentIsporuka($_GET['isporuka'],
                    $_GET['koncentratorKisika']);
               
    }
    
}