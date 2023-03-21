<?php


class Pacijent 
{
    //CRUD OPERACIJE

    public static function read()
    {

        $veza = DB::getInstance(); //read napravljen da nemogu brisati OC ako nije prikupljen
        $izraz = $veza->prepare('
        
        select a.sifra,
                a.imeprezime ,
                a.telefon ,
                a.datumRodenja ,
                a.adresa ,
                a.oib ,
                a.pacijentKomentar ,
                count(b.sifra) as prikupljen
        from pacijent a
        left join prikup b on a.sifra = b.pacijent 
        group by a.imeprezime ,
                a.telefon ,
                a.datumRodenja ,
                a.adresa ,
                a.oib ,
                a.pacijentKomentar
        order by a.imeprezime  asc;

        ');
        $izraz->execute();
        return $izraz->fetchAll();
    }

    public static function readOne($sifra)
    {
        
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
            select * from pacijent
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
        values(:imeprezime,:telefon,:datumRodenja,:adresa,:oib,:pacijentKomentar);

        ');//dvotocke moraju odgovarat vrijednosti name od inputa
        $izraz->execute($parametri);
    }

    public static function update($parametri)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        update pacijent set
            imeprezime=:imeprezime,
            telefon =:telefon,
            datumRodenja =:datumRodenja,
            adresa =:adresa,
            oib =:oib,
            pacijentKomentar =:pacijentKomentar
        where sifra=:sifra


        ');//dvotocke moraju odgovarat vrijednosti name od inputa
        $izraz->execute($parametri);
    }

    public static function delete($sifra)
    {
        
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
            delete from pacijent
            where sifra=:sifra
        
        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);
        $izraz->execute();
    }
}