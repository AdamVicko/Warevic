<?php

class Isporuka
{
    // CRUD operacije

    public static function read()
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select 
            a.sifra, a.datumIsporuke, b.imeprezime, c.serijskiKod
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
        where sifra=:sifra;
        
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
        
        insert into isporuka
        (datumIsporuke,imeprezime,serijskiKod)
        values
        (:datumIsporuke,:imeprezime,:serijskiKod);
        
        ');
        $izraz->execute($parametri);
    }

    public static function update($parametri)
    {
        Log::info($parametri);
        unset($parametri['polaznici']);
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
            update isporuka set
            datumIsporuke=:datumIsporuke,
            imeprezime=:imeprezime,
            serijskiKod=:serijskiKod
            where sifra=:sifra
        
        ');
        $izraz->execute($parametri);
    }

    public static function delete($sifra)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
            delete from isporuka
            where sifra=:sifra
        
        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);
    }
}

