<?php
//6
class View //ubacivanje sadrzaja u predlozak odnosno slanje sarzaja za svaki drugi view pacijent,prijava...
{

    private $predlozak;
    private $CSSdependency=null;
    private $JSdependency=null;

    public function __construct($predlozak='predlozak')
    {
        $this->predlozak = $predlozak;
    }

    public function render($phtmlStranica,$parametri=[])
    {
        $cssDatoteka = BP . 'public' .
        DIRECTORY_SEPARATOR . 'css' .
        DIRECTORY_SEPARATOR . $phtmlStranica . '.css';
        if(file_exists($cssDatoteka)){
            $css=str_replace('\\','/',$phtmlStranica) . '.css';
        }

        $jsDatoteka = BP . 'public' .
        DIRECTORY_SEPARATOR . 'js' .
        DIRECTORY_SEPARATOR . $phtmlStranica . '.js';
        if(file_exists($jsDatoteka)){
            $js=str_replace('\\','/',$phtmlStranica) . '.js';
        }

        $viewDatoteka = BP_APP . 'view' . 
        DIRECTORY_SEPARATOR . $phtmlStranica . '.phtml'; // provjerava dal se nalazi ta datoteka u view direktoriju
        ob_start(); // nemoj to slat klijentu (cache)
        extract($parametri); // parametri su asocijativni niz a funkcija extrakt ce napravit od kljuceva varijable s vrijednostima
        //echo $viewDatoteka;
        if(file_exists($viewDatoteka))
        {
            include_once $viewDatoteka; // ne radimo autolload sa ovim datotekama jer nam treba extrakt parametara odnosno varijable(slijed)
        }
        else
        {
            include_once BP_APP . 'view' . 
            DIRECTORY_SEPARATOR . 'errorViewDatoteka.phtml';
        }
        $sadrzaj=ob_get_clean(); // vrijednost unutar datoteke index.phtml,prijava.phtml... dodjeljuje varijabli sadrzaj
        include_once BP_APP . 'view' . DIRECTORY_SEPARATOR . 
        $this->predlozak . '.phtml'; // pozivam view datoteko predlozak.phtml u kojem ispisujem sadrzaj dostupan samo u predlozku!
    }

    public function api($parametri){
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($parametri,JSON_NUMERIC_CHECK);
    }


}