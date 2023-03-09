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
        return $ruta; // dobivamo ostatak zeljenog patha odnosno zeljenu rutu kretanja
    }
}