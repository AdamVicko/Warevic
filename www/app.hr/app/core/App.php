<?php

//2

class App
{
    //ova metoda ce saznat sto zelim pokrenuti i to ce pokrenuti znaci trazi /klasa/metoda
    //ne treba pamtiti stanje stoga nije constructor
    public static function start()
    {
        //echo '<pre>';
        //print_r($_SERVER);
        //echo '</pre>';

        $ruta = Request::getRuta(); // dobivamo autoload ove klase

        //Log::info($ruta);

        $djelovi = explode('/',substr($ruta,1)); // podjeli string i spremi u array te djelove

        //Log::info($djelovi);

        //idemosaznat kontrolerr
        $controller = '';

        if(!isset($djelovi[0]) || $djelovi[0]==='') // ako nije!
        {
            $controller='IndexController';
        }
        else
        {
            $controller = ucfirst($djelovi[0]) . 'Controller'; //ucfirst prvo slovo veliko
        }                                                       //dodjeljujem im Controller zbog nazivanja klasa da sva bude jednistvena
                                                                //u controlleru ce svaka klasa imati Controller na kraju
        //Log::info($controller);
        
        //idemo saznat metodu
        $metoda='';
        if(!isset($djelovi[1]) || $djelovi[1]==='')
        {
            $metoda='index';
        }
        else
        {
            $metoda=$djelovi[1];
        }
        //Log::info($metoda);


        if(!(class_exists($controller) && method_exists($controller,$metoda)))
        {
            echo 'Ne postoji ' . $controller . '-&gt;' . $metoda;
            return;
        }

        //izvodenje
        $instanca = new $controller();
        $instanca->$metoda(); //kao POINTERI U C-u !!!!!!!!!!
    
    }

    public static function config($kljuc)
    {
        $configFile = BP_APP . 'konfiguracija.php';

        if( !file_exists($configFile))
        {
            return 'Konfiguracijska datoteka ne postoji!';
        }

        $config = require $configFile;
        
        if(!isset($config[$kljuc]))
        {
            return 'Kljuc ' . $kljuc . ' nije postavljen u konfiguraciji!';
        }

        return $config[$kljuc];
    }
}