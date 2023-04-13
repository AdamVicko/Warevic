<?php


class IndexController extends AutorizacijaController //4
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

}