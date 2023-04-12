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

    public function pocetniPodaci()
    {
        $e = new stdClass();
        $e->datumIsporuke='';
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
        $ui = Isporuka::ukupnoIsporuka($uvjet);
        $zadnja = (int)ceil($ui/App::config('brps')); // ceil= zaokruzi na prvi veci cijeli broj ako je decimalno
        $this->view->render($this->viewPutanja . 
            'index',[
                'podaci'=>Isporuka::read($uvjet,$stranica),
                'css' => 'isporuka.css',
                'stranica' => $stranica,
                'uvjet' => $uvjet, // ucitavam uvjet
                'zadnja' => $zadnja
            ]);
            
    }

    public function novi()
    {   // pacijent sifra sluzi da ako nema niti jednog pacijenta da te automatski baci na kreaciju pacijente ili kisika
        $pacijentSifra = Pacijent::prviPacijent();
        //log::info($pacijentSifra);
        if($pacijentSifra==0){
         header('location: ' . App::config('url') . 'pacijent/index?p=1');// saljjem poruke ovim putem
        }
        
        $koncentratorSifra = KoncentratorKisika::prviKoncentrator();
        if($koncentratorSifra==0){
         header('location: ' . App::config('url') . 'koncentratorKisika/index?p=2');// saljjem poruke ovim putem
        }
        
        //Moze i ovako
        header('location: ' . 
        App::config('url') . 'Isporuka/promjena/' .
        Isporuka::create([
            'datumIsporuke'=>'',
            'pacijent'=>$pacijentSifra, 
            'koncentratorKisika'=>$koncentratorSifra
        ])); // kreiram odma isporuku kako bi ju mogo napunit s pacijentom i koncentratorom kisika
        
        //$this->promjena(Isporuka::create([
          //  'datumIsporuke'=>''
            //'pacijent'=>$pacijentSifra,// najbolje je tako a ne fiksno stavljat 1 jel se moze desit da se 1 izbrise!!
            //'koncentratorKisika'=>$koncentratorSifra
        //])); ///////////////////////////// tru si stao provjeri jel ti to radi bar da ucita kao i njegovo na videu zatim ide promjena!!!!!!!
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

    public function odustani($sifra='')
    {
        $e=Isporuka::readOne($sifra);
        
        if(empty($e->pacijentSifra)&&(empty($e->kisikSifra)))
        {
            Isporuka::delete($e->sifra);
        }
        header('location: ' . App::config('url') . 'isporuka/index');
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

        if($_SERVER['REQUEST_METHOD']==='GET'){
            $this->promjena_GET($sifra);
            return;
        }
        
        $this->view->render
        (
            $this->viewPutanja . 'detalji',
            [
                'e' =>$this->e
            ]
        );/*

        }
        $this->e = (object)$_POST;
        try {
            $this->e->sifra=$sifra;
            $this->kontrola();
            $this->pripremiZaBazu();
            Isporuka::update((array)$this->e);
            header('location:' . App::config('url') . 'isporuka');
           } catch (\Exception $th) {
            $this->view->render($this->viewPutanja .
            'detalji',[
                'poruke'=>$this->poruke,
                'pacijent'=>$this->definirajPacijenta(),
                'koncentratorKisika'=>$this->definirajKoncentratorKisika(),
                'e'=>$this->e
            ]);
           }
        
        /*$this->e = Isporuka::readOne($sifra);
        $pacijenti = [];
        $p = new stdClass();
        $p->sifra=0;
        $p->imeprezime='Not selected!';
        $pacijenti[]=$p;
        foreach(Pacijent::read() as $pacijent){
        $pacijenti[]=$pacijent;
    }

        $this->view->render($this->viewPutanja .
            'detalji',[
                'e'=>$this->e,
                'poruke'=>$this->poruke,
                'pacijenti' => $pacijenti,
                'pacijent'=>$this->definirajPacijenta(),
                'koncentratorKisika'=>$this->definirajKoncentratorKisika()
            ]);*/

       /* if($_SERVER['REQUEST_METHOD']==='GET'){
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
            header('location:' . App::config('url') . 'isporuka');
           } catch (\Exception $th) {
            $this->view->render($this->viewPutanja .
            'detalji',[
                'poruke'=>$this->poruke,
                'pacijent'=>$this->definirajPacijenta(),
                'koncentratorKisika'=>$this->definirajKoncentratorKisika(),
                'e'=>$this->e
            ]);
           }     */   
    }
    private function kontrola()
    {
        return true;
        /*
        $s = $this->e->datumIsporuke;
        if(strlen(trim($s))===0){
            $this->poruke['datumIsporuke']='Delivery date is mandatory!';
            throw new Exception();
        }*/
    }
    private function promjena_GET($sifra)
    {
        $this->e = Isporuka::readOne($sifra);
      
       if($this->e->datumIsporuke!=null){
        $this->e->datumIsporuke = date('Y-m-d',strtotime($this->e->datumIsporuke));
       }
       $this->view->render($this->viewPutanja . 
       'detalji',[
           'e'=>$this->e
       ]); 
    }


   

   
    public function pripremiZaView()
    {
       // $this->e = (object)$_POST;
    }

    public function pripremiZaBazu()
    {

       // if($this->e->datumIsporuke==''){
       //     $this->e->datumIsporuke=null;
       // }
   
    }

   


    /*
    public function dodajKoncentratorKisika()
    {
        //prvo se trebala pozabaciti postoji li u $_GET
        // traženi parametri
        $res = new stdClass();
        if(!Isporuka::postojiKoncentratorKisikaIsporuka($_GET['isporuka'],
                    $_GET['koncentratorKisika'])){
                        Isporuka::postojiKoncentratorKisikaIsporuka($_GET['isporuka'],
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
        //prvo se trebala pozabaviti postoji li u $_GET traženi parametri

        $res = new stdClass();
        if(!Isporuka::postojiPacijentIsporuka(($_GET['isporuka']),
                    ($_GET['pacijent']))){
                        Isporuka::postojiPacijentIsporuka($_GET['isporuka'],
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

    public function obrisipacijenta($sifra)
    {
        $podaci= Isporuka::
        Isporuka::obrisiPacijentIsporuka();
               
    }

    public function obrisiKoncentratorKisika()
    {
        //prvo se trebala pozabaciti postoji li u $_GET
        // traženi parametri

        Isporuka::obrisiKoncentratorKisikaIsporuka($_GET['isporuka'],
                    $_GET['koncentratorKisika']);
             
    }*/
    
}
