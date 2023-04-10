<?php


class Pacijent 
{
    //CRUD OPERACIJE

    public static function read($uvjet='',$stranica=1)
    {

        $uvjet = '%' . $uvjet . '%';
        $brps = App::config('brps');
        $pocetak = ($stranica * $brps) - $brps;

        $veza = DB::getInstance(); //read napravljen da nemogu brisati OC ako nije prikupljen 
        $izraz = $veza->prepare('
        
        select  a.sifra,
                a.imeprezime ,
                a.telefon ,
                a.datumRodenja ,
                a.adresa ,
                a.oib ,
                a.pacijentKomentar ,
                count(b.sifra) as prikupljen,
                count(c.isporuka) as isporucen
        from pacijent a
        left join prikup b on a.sifra = b.pacijent
        left join isporukapacijent c on a.sifra = c.pacijent
        where concat(a.imeprezime, \' \', ifnull(a.oib,\'\'))
        like :uvjet
        group by a.sifra, 
                a.imeprezime ,
                a.telefon ,
                a.datumRodenja ,
                a.adresa ,
                a.oib ,
                a.pacijentKomentar
        order by a.imeprezime asc limit :pocetak, :brps;

        '); // ne radi se execute zbog toga sto su mi pocetak i brps vrijednosti a ne parametri te ih execute nece odradit kao sto bi uvjet
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
        
            select * from pacijent
            where sifra=:sifra
        
        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);
        return $izraz->fetch();
    }

    public static function ukupnoPacijenata($uvjet='')
    {
        $uvjet = '%' . $uvjet . '%';
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select count(*)
        from 
        pacijent a
        where concat(a.imeprezime, \' \', 
        ifnull(a.oib,\' \'))
        like :uvjet;
        
        ');
        $izraz->execute([
            'uvjet'=>$uvjet
        ]);
        return $izraz->fetchColumn();
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

            select pacijent 
            from isporukaPacijent 
            where pacijent=:sifra;
        
        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);

        $izraz = $veza->prepare('
        
        delete from isporukaPacijent
        where pacijent=:sifra
    
        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);
        
        $izraz = $veza->prepare('

        select pacijent 
        from prikup 
        where sifra=:sifra;
    
        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);

        $izraz = $veza->prepare('
        
        delete from prikup
        where sifra=:sifra

        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);

        $izraz = $veza->prepare('
        
        delete from pacijent
        where sifra=:sifra
    
        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);

    }

    public static function prviPacijent()
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select sifra from pacijent
        order by sifra limit 1;

        ');//dvotocke moraju odgovarat vrijednosti name od inputa
        $izraz->execute();
        $imeprezime=$izraz->fetchColumn();
        return (int)$imeprezime;
    }


}