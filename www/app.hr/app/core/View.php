<?php

class View
{

    private $predlozak;

    public function __construct($predlozak='predlozak')
    {
        $this->predlozak = $predlozak;
    }

    public function render($phtmlStranica,$parametri=[])
    {
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
        $sadrzaj=ob_get_clean(); // vrijednost unutar datoteke index.phtml dodjeljuje varijabli sadrzaj
        include_once BP_APP . 'view' . DIRECTORY_SEPARATOR . 
        $this->predlozak . '.phtml'; // pozivam view datoteko predlozak.phtml u kojem ispisujem sadrzaj
    }

}