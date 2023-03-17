<?php


class Prikup 
{
    //CRUD OPERACIJE

    public static function read()
    {

        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select a.datumPrikupa, b.imeprezime ,b.datumRodenja ,b.oib ,b.pacijentKomentar,b.telefon , c.serijskiKod,c.radniSat
        from prikup a 
        inner join pacijent b on a.pacijent = b.sifra  
        inner join koncentratorkisika c on a.koncentratorKisika = c.sifra  
        order by datumPrikupa asc;

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
        
        insert into koncentratorkisika (serijskiKod,radniSat ,ocKomentar)
        values (:serijskikod,:radniSat,:ockomentar)

        insert into prikup(datumPrikupa)
        values (:datumprikupa)

        ');//dvotocke moraju odgovarat vrijednosti name od inputa
        $izraz->execute($parametri);
    }
}