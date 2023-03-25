<?php


class Isporuka 
{
    //CRUD OPERACIJE

    public static function read()
    {

        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select 
            a.sifra,a.datumIsporuke, b.imeprezime ,b.datumRodenja,
            b.oib ,b.telefon ,b.adresa,b.pacijentKomentar, 
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
        
        select 
            a.sifra, a.datumIsporuke, b.sifra, b.imeprezime ,b.datumRodenja,
            b.oib ,b.telefon ,b.adresa,b.pacijentKomentar, 
            c.serijskiKod,c.radniSat,c.ocKomentar,c.sifra
        from isporuka a 
            inner join pacijent b on a.pacijent = b.sifra  
            inner join koncentratorKisika c on a.koncentratorKisika = c.sifra
        where a.sifra=:sifra
        order by datumIsporuke asc;
        
        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);
        return $izraz->fetch();
    }

    public static function create($parametri)
    {
        $veza = DB::getInstance();
        $veza->beginTransaction();

        $izraz = $veza->prepare('
        insert into pacijent(imeprezime,telefon,datumRodenja,adresa,oib,pacijentKomentar)
        values(:imeprezime,:telefon,:datumRodenja,:adresa,:oib,:pacijentKomentar)
        ');//dvotocke moraju odgovarat vrijednosti name od inputa
        $izraz->execute(
        [
            'imeprezime'=>$parametri['imeprezime'],
            'telefon'=>$parametri['telefon'],
            'datumRodenja'=>$parametri['datumRodenja'],
            'adresa'=>$parametri['adresa'],
            'oib'=>$parametri['oib'],
            'pacijentKomentar'=>$parametri['pacijentKomentar']
        ]);

        $sifraPacijent = $veza->lastInsertId();//sifra za pacijent
        $izraz=$veza->prepare('
        insert into koncentratorKisika (serijskiKod,radniSat ,ocKomentar)
        values (:serijskiKod,:radniSat,:ocKomentar)
        ');
        $izraz->execute(
            [
                'serijskiKod'=>$parametri['serijskiKod'],
                'radniSat'=>$parametri['radniSat'],
                'ocKomentar'=>$parametri['ocKomentar'],
            ]);


        $sifraKoncentratorKisika = $veza->lastInsertId();//sifra za koncentrator kisika
        $izraz=$veza->prepare('
        insert into isporuka(datumPrikupa,pacijent,koncentratorKisika)
        values (:datumPrikupa,:pacijent,:koncentratorKisika)
        ');

        $izraz->execute(
            [
                'datumPrikupa'=>$parametri['datumPrikupa'],
                'pacijent'=>$sifraPacijent,
                'koncentratorKisika'=>$sifraKoncentratorKisika
            ]);

        $veza->commit();
    }

    public static function update($parametri)
    {
        $veza = DB::getInstance();
        $veza->beginTransaction();

        $izraz = $veza->prepare('
            select pacijent from isporuka where sifra=:sifa; 
        ');//dvotocke moraju odgovarat vrijednosti name od inputa
        $izraz->execute([
            'sifra'=>$parametri['sifra']
        ]);
        $sifraPacijent = $izraz->fetchColumn();
        $izraz = $veza->prepare('
            update pacijent set
                    imeprezime=:imeprezime,
                    telefon=:telefon,
                    datumRodenja=:datumRodenja,
                    adresa=:adresa,
                    oib=:oib,
                    pacijentKomentar=:pacijentKomentar
            where sifra=:sifra
        ');
        $izraz->execute([
            'sifra'=>$sifraPacijent,
            'imeprezime'=>$parametri['imeprezime'],
            'telefon'=>$parametri['telefon'],
            'datumRodenja'=>$parametri['datumRodenja'],
            'adresa'=>$parametri['adresa'],
            'oib'=>$parametri['oib'],
            'pacijentKomentar'=>$parametri['pacijentKomentar']
        ]);


        $izraz = $veza->prepare('
            select koncentratoKisika from prikup where sifra=:sifa; 
        ');//dvotocke moraju odgovarat vrijednosti name od inputa
        $izraz->execute([
            'sifra'=>$parametri['sifra']
        ]);
        $sifraKoncentratorKisika = $izraz->fetchColumn();
        $izraz = $veza->prepare('
        update koncentratoKisika set
            serijskiKod=:serijskiKod,
            radniSat=:radniSat,
            ocKomentar=:ocKomentar
        where sifra=:sifra
        ');
        $izraz->execute([
            'sifra'=>$sifraKoncentratorKisika,
            'serijskiKod'=>$parametri['serijskiKod'],
            'radniSat'=>$parametri['radniSat'],
            'ocKomentar'=>$parametri['ocKomentar']
        ]);


        $izraz = $veza->prepare('
        update isporuka set
            datumIsporuke=:datumIsporuke
        where sifra=:sifra
        ');
        $izraz->execute([
            'sifra'=>$parametri['sifra'],
            'datumIsporuke'=>$parametri['datumIsporuke']
        ]);
            
            $veza->commit();
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
        $izraz->execute();
    }

    public static function postojiIstiOIB($oib,$sifra=0)
    {
        if($sifra>0){
            $sql = ' select count(b.sifra) 
            from isporuka a inner join osoba b
            on a.pacijent=b.sifra where b.oib=:oib ';
        }else{
            $sql = ' select count(a.sifra) 
            from pacijent a where a.oib=:oib ';
        }

        if($sifra>0){
            $sql.=' and a.sifra!=:sifra';
        }

        $veza = DB::getInstance();
        $izraz = $veza->prepare($sql);

        $parametri=[];
        $parametri['oib']=$oib;

        if($sifra>0){
            $parametri['sifra']=$sifra;
        }

        $izraz->execute($parametri);
        $sifra=$izraz->fetchColumn();
        return $sifra==0;
    }
}