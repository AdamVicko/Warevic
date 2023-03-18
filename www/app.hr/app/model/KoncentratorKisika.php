<?php


class KoncentratorKisika 
{
    //CRUD OPERACIJE

    public static function read()
    {

        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select * from koncentratorKisika
        order by datumKupovine asc;

        ');
        $izraz->execute();
        return $izraz->fetchAll();
    }

    public static function create($parametri)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        insert into koncentratorkisika (serijskiKod,radniSat ,proizvodac,model,ocKomentar,datumKupovine)
        values(:serijskiKod,:radniSat,:proizvodac,:model,:ocKomentar,:datumKupovine);

        ');//dvotocke moraju odgovarat vrijednosti name od inputa
        $izraz->execute($parametri);
    }


}