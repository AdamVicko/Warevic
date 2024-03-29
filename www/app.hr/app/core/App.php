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

        if(!isset($djelovi[0]) || $djelovi[0]==='') // ako nema niceg ili ako je prazno
        {
            $controller='PrijavaController';
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
            $metoda='prijava';
        }
        else
        {
            $metoda=$djelovi[1];
        }
        //Log::info($metoda);

        $parametar='';
        if(!isset($djelovi[2]) || $djelovi[2]==='')
        {
            $parametar='';
        }
        else
        {
            $parametar=$djelovi[2];
        }

        if(!(class_exists($controller) && method_exists($controller,$metoda)))
        {
            $v = new View();
            $v->render('notFoundController',
            [
                'poruka'=>'Ne postoji ' . $controller . '-&gt;' . $metoda
            ]);
            //echo 'Ne postoji ' . $controller . '-&gt;' . $metoda;
            return;
        }
        //izvodenje akpo postoji treci parametar 
        $instanca = new $controller();
        if(strlen($parametar)>0)
        {
            $instanca->$metoda($parametar);
        }
        else
        {
            $instanca->$metoda();//kao POINTERI U C-u !!!!!!!!!!
        }
       
    
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

    public static function auth()
    {
        return isset($_SESSION['auth']); // ako sam ulogiran true a ako nisam onda false
    }

    public static function djelatnik()
    {
        return $_SESSION['auth']->imeprezime;
    }

    public static function admin()
    {
        return $_SESSION['auth']->uloga==='administrator';
    }

    public static function dev()
    {
        return App::config('dev');
    }
}