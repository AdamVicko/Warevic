<?php

class Isporuka
{
    // CRUD operacije

    public static function read()
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select
            sifra,datumIsporuke 
        from isporuka
        group by 
            sifra,datumIsporuke 
        order by datumIsporuke asc;
        
        ');
        $izraz->execute();
        $rez = $izraz->fetchAll(); 
        foreach($rez as $r){
            $r->pacijent=Isporuka::pacijentNaIsporuki($r->sifra);
            $r->koncentratorKisika=Isporuka::koncentratorKisikaNaIsporuki($r->sifra);
        }
        //log::info($rez);
        return $rez;
    }


    public static function pacijentNaIsporuki($sifra)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select 
            a.sifra,a.imeprezime,a.adresa,a.telefon
        from pacijent a 
            inner join isporukapacijent b on b.pacijent =a.sifra 
        where b.isporuka=:sifra;
        
        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);
        return $izraz->fetchAll();
    }

    public static function  koncentratorKisikaNaIsporuki($sifra)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select 
            a.sifra,a.serijskiKod,a.radniSat ,a.proizvodac,a.model
        from koncentratorkisika a 
            inner join isporukakoncentratorkisika b on b.koncentratorKisika  =a.sifra 
        where b.isporuka=:sifra;
        
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

        $isporuka = $izraz->fetch();

        
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select 
            b.sifra , b.imeprezime 
        from isporukapacijent a 
            inner join pacijent b on b.sifra  = a.pacijent 
            inner join isporuka c on c.sifra = a.isporuka  
        where a.isporuka =:sifra;
        
        ');
        $izraz->execute([ //dovuko sam osnovne podatke
            'sifra'=>$sifra
        ]);

        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select 
            b.sifra , b.serijskiKod 
        from isporukaKoncentratorKisika a 
            inner join koncentratorKisika b on b.sifra  = a.koncentratorKisika 
            inner join isporuka c on c.sifra = a.isporuka  
        where a.isporuka =:sifra;
        
        ');
        $izraz->execute([ //dovuko sam osnovne podatke
            'sifra'=>$sifra
        ]);

        $isporuka->pacijent = $izraz->fetchAll();// vraca array std objekata znaci da ce mi pacijent biti kao objekt u arrayu
        $isporuka->koncentratorKisika = $izraz->fetchAll();// vraca array std objekata znaci da ce mi pacijent biti kao objekt u arrayu
        return $isporuka;
        
    }

    public static function create($parametri)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        insert into isporuka
        (datumIsporuke)
        values
        (:datumIsporuke);
        
        ');
        $izraz->execute($parametri);
        return $veza->lastInsertId();
    }

    public static function update($parametri)
    {
        //Log::info($parametri);
        unset($parametri['pacijent']);
        unset($parametri['koncentratorKisika']);
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
            update isporuka set
            datumIsporuke=:datumIsporuke
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

/*
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
        $rez = (int)$izraz->fetchColumn(); // zbog problematike vraanja podatka moguce da bude u stringu mi cemo ga odma bacat u int!
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
        
           insert into isporuka (isporuka,pacijent)
           values (:isporuka, :pacijent)
        
        ');
        $izraz->execute([
            'isporuka'=>$isporuka,
            'pacijent'=>$pacijent
        ]);
    }

    public static function dodajKoncentratorKisikaIsporuka($isporuka, $koncentratorKisika)
    {   
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
           insert into isporuka (isporuka,koncentratorKisika)
           values (:isporuka, :koncentratorKisika)
        
        ');
        $izraz->execute([
            'isporuka'=>$isporuka,
            'koncentratorKisika'=>$koncentratorKisika
        ]);
    }


    public static function obrisiPacijentIsporuka($isporuka, $pacijent)
    {   
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
            DELETE FROM isporuka
            WHERE pacijent = :pacijent
        ');
        $izraz->execute([
            'pacijent' => $pacijent
        ]);
    }

    public static function obrisiKoncentratorKisikaIsporuka($isporuka, $koncentratorKisika)
    {   
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
           delete from isporuka
           where isporuka=:isporuka
           and koncentratorKisika=:koncentratorKisika
        
        ');
        $izraz->execute([
            'isporuka'=>$isporuka,
            'koncentratorKisika'=>$koncentratorKisika
        ]);
    }
    */
}

