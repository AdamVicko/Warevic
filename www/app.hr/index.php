<?php

//1

//ova datoteka ce definirati temeljne preduvjete i napraviti autoloading

define('BP',__DIR__ . DIRECTORY_SEPARATOR);
//BP je base path
define('BP_APP', BP . 'app' . DIRECTORY_SEPARATOR);

$zaAutoload = [
    BP_APP . 'controller',
    BP_APP . 'core',
    BP_APP . 'model'
]; //view ne ide u autoload jer su u njemu phtml datoteke


$putanje = implode(PATH_SEPARATOR, $zaAutoload);
//echo PATH_SEPARATOR; ispisuje ; znaci path separator za windows je ; ( koristim oga zbog toga sto je univerzalan za linux i windowse)

set_include_path($putanje); // dodali smo mu da je i to included path plus onaj u xampp-u
//php kad krene njemu je include path zadan od xampp-a (c/xampp/php)

spl_autoload_register(function($klasa){// bezimena anonymus function
                                        //ova funkcija se brine samo za kalse ako nema vraca 404
                                        //ako pronade zeljenu datoteku nju includa
    //echo 'u spl_autoload, trazim klasu ' . $klasa . '<br>';
    $putanje = explode(PATH_SEPARATOR, get_include_path());//dohvacam putanje za ovu funkciju
    foreach($putanje as $putanja)
    {
        //echo $putanja . '<br>';
        $datoteka = $putanja . DIRECTORY_SEPARATOR . $klasa . '.php'; //kreirali smo gotovu putanju(path do datoteke)
        //echo $datoteka . '<br>';
        if(file_exists($datoteka))
        {
            require_once $datoteka;
            break;
        }
    }
});

 App::start(); // satrta aplikaciju  
                //okida (starta) funkciju spl_autoload_register

//$o = new Osoba(); //new Osoba pokrece constructor
//echo $o->getIme();
