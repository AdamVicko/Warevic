<?php

class IsporukaController 
extends AutorizacijaController
implements ViewSucelje
{

    private $viewPutanja = 'privatno' . 
    DIRECTORY_SEPARATOR . 'isporuka' . 
    DIRECTORY_SEPARATOR;
    private $e;

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

    public function novaIsporuka()
    {
        Isporuka::novaIsporuka();
        $this->index();
    }

    public function azurirajIsporuku($sifra)
    {
        Isporuka::azurirajIsporuku($sifra);
        $this->index();
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
        header('location: ' . App::config('url') . 'isporuka/index');
    }

   
    public function promjena($sifra='')
    {
        $pacijenti=Pacijent::read();
        $koncentratoriKisika=KoncentratorKisika::read();
        $trenutniPodaciIsporuke = Isporuka::readOne($sifra);
        $this->view->render($this->viewPutanja . 
            'detalji',[
            'pacijenti'=> $pacijenti,
            'koncentratoriKisika' => $koncentratoriKisika,
            'trenutniPodaciIsporuke' => $trenutniPodaciIsporuke
       ]);
    }
    public function pripremiZaView()
    {
       
    }
    public function pripremiZaBazu()
    {

    }
}
