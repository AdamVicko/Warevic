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
            group by 	a.sifra, 
                        a.datumIsporuke,
                        b.imeprezime,
                        c.serijskiKod
        order by datumIsporuke asc;
        
        ');
        $izraz->execute();
        return $izraz->fetchAll();
    }

    public static function  koncentratorKisikaNaIsporuki($sifra)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select b.serijskiKod 
        from isporuka a inner join 
        koncentratorKisika b on a.koncentratorKisika =b.sifra 
        where koncentratorKisika=:sifra;
        
        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);
        return $izraz->fetchAll();
    }
    public static function pacijentNaIsporuki($sifra)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select b.imeprezime 
        from isporuka a inner join 
        pacijent b on a.pacijent =b.sifra 
        where pacijent=:sifra;
        
        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);
        return $izraz->fetchAll();
    }

    public static function readOne($sifra)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select * from isporuka
        where sifra=:sifra;
        
        ');
        $izraz->execute([ //dovuko sam osnovne podatke
            'sifra'=>$sifra
        ]);

        $isporuka = $izraz->fetch();// isporuka je std objekt

        $izraz = $veza->prepare('
        
        select  a.sifra, b.imeprezime as pacijent , c.serijskiKod as koncentratorKisika 
        from isporuka a 
        inner join pacijent b on a.pacijent = b.sifra 
        inner join koncentratorKisika c on a.koncentratorKisika = c.sifra  
        where a.sifra=:sifra;
        
        ');
        $izraz->execute([//tu sam dovuko sve o pacijentu da ga mogu prikazat na viewu
            'sifra'=>$sifra
        ]);

        $isporuka->pacijenti = $izraz->fetchAll();//fetchAll vraca array std objekta!!!!! meni ce vratit samo jedan 0
       
        return $isporuka;

    }

    public static function create($parametri)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        insert into isporuka
        (datumIsporuke,pacijent,koncentratorKisika)
        values
        (:datumIsporuke,:pacijent,:koncentratorKisika);
        
        ');
        $izraz->execute($parametri);
        return $veza->lastInsertId();
    }

    public static function update($parametri)
    {
        Log::info($parametri);
        unset($parametri['pacijent']);
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


    public static function postojiPacijentIsporuka($isporuka, $pacijent)
    {   
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select count(*) as ukupno 
        from isporuka where pacijent=:pacijent 
        
        ');
        $izraz->execute([
            'isporuka'=>$isporuka,
            'pacijent'=>$pacijent
        ]);
        $rez = (int)$izraz->fetchColumn();
        return $rez>0;
    }

    public static function postojiKoncentratorKisikaIsporuka($isporuka, $koncentrator)
    {   
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select count(*) as ukupno 
        from isporuka where koncentrator=:koncentrator
        
        ');
        $izraz->execute([
            'isporuka'=>$isporuka,
            'koncentrator'=>$koncentrator
        ]);
        $rez = (int)$izraz->fetchColumn();
        return $rez>0;

    }

    public static function dodajPacijentIsporuka($isporuka, $pacijent)
    {   
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
           insert into isporuka (grupa,polaznik)
           values (:grupa, :polaznik)
        
        ');
        $izraz->execute([
            'isporuka'=>$isporuka,
            'polaznik'=>$pacijent
        ]);
    }

    public static function dodajKoncentratorKisikaIsporuka($isporuka, $koncentratorKisika)
    {   
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
           insert into clan (grupa,polaznik)
           values (:grupa, :polaznik)
        
        ');
        $izraz->execute([
            'isporuka'=>$isporuka,
            'polaznik'=>$koncentratorKisika
        ]);
    }


    public static function obrisiPacijentIsporuka($isporuka, $pacijent)
    {   
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
           delete from clan where grupa=:grupa
           and polaznik=:polaznik
        
        ');
        $izraz->execute([
            'isporuka'=>$isporuka,
            'polaznik'=>$pacijent
        ]);
    }
}

