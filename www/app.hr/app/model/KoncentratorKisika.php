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

    public static function readOne($sifra)
    {

        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select * from koncentratorKisika
        where sifra=:sifra

        ');
        $izraz->execute(
            [
                'sifra' =>$sifra
            ]);
        return $izraz->fetch();
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

    public static function postojiIstiUBazi($s)
    {
        echo $s;
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select sifra from koncentratorKisika
        where serijskiKod = :serijskiKod

        ');
        $izraz->execute([
            'serijskiKod'=>$s
        ]);
        $sifra=$izraz->fetchColumn();
        return $sifra>0;
    }


}