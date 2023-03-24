<?php


class Isporuka 
{
    //CRUD OPERACIJE

    public static function read()
    {

        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select 
        a.datumIsporuke , b.imeprezime ,b.datumRodenja ,b.oib ,
        b.telefon ,b.adresa,b.pacijentKomentar, 
        c.serijskiKod,c.radniSat,c.ocKomentar
        from isporuka a 
        inner join pacijent b on a.pacijent = b.sifra  
        inner join koncentratorKisika c on a.koncentratorKisika = c.sifra  
        order by datumIsporuke asc;

        ');
        $izraz->execute();
        return $izraz->fetchAll();
    }

    public static function readOne($sifra)
    {
        
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
            select * from isporuka
            where sifra=:sifra
        
        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);
        return $izraz->fetch();
    }

    public static function create($parametri)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        insert into pacijent(imeprezime,telefon,datumRodenja,adresa,oib,pacijentKomentar)
        values(:imeprezime,:telefon,:datumRodenja,:adresa,:oib,:pacijentKomentar)
       

        ');//dvotocke moraju odgovarat vrijednosti name od inputa
        $izraz->execute($parametri);

        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        insert into koncentratorKisika (serijskiKod,radniSat ,ocKomentar)
        values (:serijskiKod,:radniSat,:ocKomentar)
       

        ');//dvotocke moraju odgovarat vrijednosti name od inputa
        $izraz->execute($parametri);

        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        insert into isporuka(datumIsporuke,)
        values (:datumIsporuke)
       

        ');//dvotocke moraju odgovarat vrijednosti name od inputa
        $izraz->execute($parametri);
    }

}

//napravi za isporuku ko za koncentrator kisika!!!!!!!!!!!!!!!!!!!!!!