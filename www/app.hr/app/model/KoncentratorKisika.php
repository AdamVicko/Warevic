<?php


class KoncentratorKisika 
{
    //CRUD OPERACIJE

    public static function read($uvjet='',$stranica=1)
    {

        $uvjet = '%' . $uvjet . '%';
        $brps = App::config('brps');
        $pocetak = ($stranica * $brps) - $brps;

        $veza = DB::getInstance(); //read napravljen da nemogu brisati OC ako nije prikupljen
        $izraz = $veza->prepare('
        
        select a.sifra,
                a.serijskiKod,
                a.radniSat,
                a.proizvodac,
                a.model,
                a.ocKomentar,
                a.datumKupovine,
                count(b.sifra) as prikupljen,
                count(c.sifra) as isporucen
        from koncentratorKisika a
            left join prikup b on a.sifra = b.koncentratorKisika
            left join isporuka c on a.sifra = c.koncentratorKisika
        where a.serijskiKod
        like :uvjet
        group by a.sifra,
                a.serijskiKod,
                a.radniSat,
                a.proizvodac,
                a.model,
                a.ocKomentar,
                a.datumKupovine
        order by a.datumKupovine asc limit :pocetak, :brps;

        ');
        $izraz->bindValue('pocetak',$pocetak, PDO::PARAM_INT); // param int tako da mi salje int a ne string
        $izraz->bindValue('brps',$brps, PDO::PARAM_INT); // param int tako da mi salje int a ne string
        $izraz->bindParam('uvjet', $uvjet);

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
        $izraz->execute([
            'sifra'=>$sifra
        ]);
        return $izraz->fetch();
    }

    public static function create($parametri)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        insert into koncentratorKisika (serijskiKod,radniSat ,proizvodac,model,ocKomentar,datumKupovine)
        values(:serijskiKod,:radniSat,:proizvodac,:model,:ocKomentar,:datumKupovine);

        ');//dvotocke moraju odgovarat vrijednosti name od inputa
        $izraz->execute($parametri);
    }

    public static function update($parametri)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        update koncentratorKisika set
            serijskiKod=:serijskiKod,
            radniSat=:radniSat,
            proizvodac=:proizvodac,
            model=:model,
            ocKomentar=:ocKomentar,
            datumKupovine=:datumKupovine
        where sifra=:sifra


        ');//dvotocke moraju odgovarat vrijednosti name od inputa
        $izraz->execute($parametri);
    }

    public static function delete($sifra)
    {
        $veza = DB::getInstance();
        
        $izraz = $veza->prepare('

            select koncentratorKisika 
            from isporuka
            where koncentratorKisika=:sifra;
        
        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);

        $izraz = $veza->prepare('
        
        delete from isporuka
        where koncentratorKisika=:sifra
    
        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);
        
        $izraz = $veza->prepare('

        select koncentratorKisika 
        from prikup 
        where koncentratorKisika=:sifra;
    
        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);

        $izraz = $veza->prepare('
        
        delete from prikup
        where koncentratorKisika=:sifra

        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);


        $izraz = $veza->prepare('
        
        delete from koncentratorKisika
        where sifra=:sifra
    
        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);

    }

    public static function postojiIstiUBazi($s)
    {
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

    public static function ukupnoKisika($uvjet='')
    {
        $uvjet = '%' . $uvjet . '%';
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select count(*)
        from 
        koncentratorKisika
        where serijskiKod
        like :uvjet;
        
        ');
        $izraz->execute([
            'uvjet'=>$uvjet
        ]);
        return $izraz->fetchColumn();
    }

    public static function prviKoncentrator()
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select sifra from koncentratorKisika
        order by sifra limit 1;

        ');//dvotocke moraju odgovarat vrijednosti name od inputa
        $izraz->execute();
        $sifra=$izraz->fetchColumn();
        return (int)$sifra;
    }

}