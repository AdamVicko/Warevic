<?php

class Isporuka
{
    // CRUD operacije

    public static function read($uvjet='',$stranica=1)
    {

        $uvjet = '%' . $uvjet . '%';
        $brps = App::config('brps');
        $pocetak = ($stranica * $brps) - $brps;
        
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select
            a.sifra,a.datumIsporuke,d.imeprezime,e.serijskiKod 
        from isporuka a
            left join pacijent d on d.sifra = a.pacijent
            left join koncentratorkisika e on e.sifra = a.koncentratorKisika 
            where concat(a.datumIsporuke, \' \', d.imeprezime, \' \', e.serijskiKod,\'\')
            like :uvjet
        group by 
            a.sifra,a.datumIsporuke,d.imeprezime, e.serijskiKod  
        order by datumIsporuke asc
        limit :pocetak, :brps;
        
        ');
        $izraz->bindValue('pocetak',$pocetak, PDO::PARAM_INT); // param int tako da mi salje int a ne string
        $izraz->bindValue('brps',$brps, PDO::PARAM_INT); // param int tako da mi salje int a ne string
        $izraz->bindParam('uvjet', $uvjet);

        $izraz->execute();
        return $izraz->fetchAll();
    }

    public static function ukupnoIsporuka($uvjet='')
    {
        $uvjet = '%' . $uvjet . '%';
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select count(*)
        from 
        isporuka a 
        left join pacijent d on d.sifra = a.pacijent
        left join koncentratorkisika e on e.sifra = a.koncentratorKisika 
        where  concat(a.datumIsporuke, \' \', d.imeprezime, \' \', e.serijskiKod,\'\')
        like :uvjet;
        
        ');
        $izraz->execute([
            'uvjet'=>$uvjet
        ]);
        return $izraz->fetchColumn();
    }


  /*  public static function pacijentNaIsporuki($sifra)
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
    }*/

 

    public static function readOne($sifra)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select a.sifra,a.datumIsporuke,a.pacijent,a.koncentratorKisika,b.imeprezime,c.serijskiKod 
        from isporuka a
        inner join pacijent b on b.sifra=a.pacijent
        inner join koncentratorKisika c on c.sifra=a.koncentratorKisika
        where a.sifra=:sifra;
        
        ');
        $izraz->execute([ //dovuko sam osnovne podatke
            'sifra'=>$sifra
        ]);

        return $izraz->fetch();
        
        
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

