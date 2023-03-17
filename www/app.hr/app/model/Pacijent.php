<?php


class Pacijent 
{
    //CRUD OPERACIJE

    public static function read()
    {

        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select * from pacijent
        order by imeprezime asc;

        ');
        $izraz->execute();
        return $izraz->fetchAll();
    }

    public static function create($parametri)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        insert into pacijent(imeprezime,telefon,datumRodenja,adresa,oib,pacijentKomentar)
        values(:imeprezime,:telefon,:datumRodenja,:adresa,:oib,:pacijentKomentar)

        ');//dvotocke moraju odgovarat vrijednosti name od inputa
        $izraz->execute($parametri);
    }

}