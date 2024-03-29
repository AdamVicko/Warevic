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
            a.sifra,a.datumIsporuke,d.imeprezime,e.serijskiKod,a.flag
        from isporuka a
            left join pacijent d on d.sifra = a.pacijent
            left join koncentratorKisika e on e.sifra = a.koncentratorKisika 
            where concat(a.datumIsporuke, \' \', d.imeprezime, \' \', e.serijskiKod,\'\')
            like :uvjet
        group by 
            a.sifra,a.datumIsporuke,d.imeprezime, e.serijskiKod,a.flag
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
            left join koncentratorKisika e on e.sifra = a.koncentratorKisika 
        where  
            concat(a.datumIsporuke, \' \', d.imeprezime, \' \', e.serijskiKod,\'\')
        like :uvjet;
        
        ');
        $izraz->execute([
            'uvjet'=>$uvjet
        ]);
        return $izraz->fetchColumn();
    }

    public static function readOne($sifra)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select 
            a.sifra,
            a.datumIsporuke,
            b.sifra as pacijentSifra,
            b.imeprezime,
            c.sifra as kisikSifra,
            c.serijskiKod 
        from isporuka a
            inner join pacijent b on b.sifra=a.pacijent
            inner join koncentratorKisika c on c.sifra=a.koncentratorKisika
        where a.sifra=:sifra;
        
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
        (datumIsporuke)
        values
        (:datumIsporuke);
        
        ');
        $izraz->execute($parametri);
        return $veza->lastInsertId();
    }

    public static function update($parametri)
    {
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

    public static function novaIsporuka()
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        insert into isporuka 
            (datumIsporuke,pacijent,koncentratorKisika,flag)
        values 
            (:datumIsporuke,:pacijent,:koncentratorKisika,1);
        
        ');
        $izraz->execute([
            'datumIsporuke'=>$_POST['datumIsporuke'],
            'pacijent'=>$_POST['pacijent'],
            'koncentratorKisika'=>$_POST['koncentratoriKisika']
        ]);
    }

    public static function azurirajIsporuku($sifra)
    {

        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        update isporuka set datumIsporuke=:datumIsporuke, pacijent=:pacijent, koncentratorKisika=:koncentratorKisika where sifra=:sifra
        ');
        $izraz->execute([
            'datumIsporuke'=>$_POST['datumIsporuke'],
            'pacijent'=>$_POST['pacijent'],
            'koncentratorKisika'=>$_POST['koncentratoriKisika'],
            'sifra' => $sifra
        ]);

    }
}

