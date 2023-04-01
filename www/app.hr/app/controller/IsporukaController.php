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
                'podaci'=>Isporuka::read(),
            ]);   
    }



    public function novi()
    {
        $pacijentSifra = Pacijent::prviPacijent();
        if($pacijentSifra==0)
        {
            header('location: ' . App::config('url') . 'pacijent/index?p=1');
        }
        $koncentratorKisikaSifra = KoncentratorKisika::prviKoncentrator();
        if($koncentratorKisikaSifra==0)
        {
            header('location: ' . App::config('url') . 'koncentratorKisika/index');
        }
        /* RADI I NA OVAJ NACIN
        header('location: ' . App::config('url') . 'isporuka/promjena' .
        Isporuka::create(
            [
                'datumIsporuke' => '',
                'pacijent' => $pacijentSifra,
                'koncentratorKisika' => $koncentratorKisikaSifra
            ]
        ));*/
        $this->promjena(Isporuka::create(
            [
                'datumIsporuke' => '',
                'pacijent' => $pacijentSifra,
                'koncentratorKisika' => $koncentratorKisikaSifra
            ]
        ));
       
    }

   

    public function promjena($sifra='')
    {

        $this->e = Isporuka::readOne($sifra);
        $this->view->render($this->viewPutanja . 
        'detalji',[
            'e'=> $this->e,
        ]);   


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
  
    }

    public function kontrolaNovi()
    {
        return true;
    }
   

    public function pocetniPodaci()
    {
        $e = new stdClass();
        $e->datumIsporuke='';
        $e->imeprezime=0;
        $e->serijskiKod=0;
        return $e;
    }

}