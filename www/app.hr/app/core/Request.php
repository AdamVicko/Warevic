<?php

class Request //3
{
    public static function getRuta()//nazuivamo ju getRuta da se drzimo OOP ucahurivanja
    {
        $ruta = '';
        if(isset($_SERVER['REDIRECT_PATH_INFO']))
        {
            $ruta = $_SERVER['REDIRECT_PATH_INFO'];
        }
        elseif (isset($_SERVER['REQUEST_URI'])) 
        {
            $ruta = $_SERVER['REQUEST_URI'];
        }

        if(strpos($ruta, '?')>=0)
        {
            $ruta = explode('?',$ruta)[0];// link na fejsu dodaje upitnik a nama treba sve s lijev strane zato indeks 0
        }

        return $ruta; // dobivamo ostatak zeljenog patha odnosno zeljenu rutu kretanja  
    }
}