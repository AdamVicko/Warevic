<?php


class Prikup 
{
    //CRUD OPERACIJE

    public static function read($uvjet='',$stranica=1)
    {
        $uvjet = '%' . $uvjet . '%';
        $brps = App::config('brps');
        $pocetak = ($stranica * $brps) - $brps;

        $veza = DB::getInstance();
        $izraz = $veza->prepare('

        select
            a.sifra,a.datumPrikupa,d.imeprezime,e.serijskiKod 
        from prikup a
            left join pacijent d on d.sifra = a.pacijent
            left join koncentratorKisika e on e.sifra = a.koncentratorKisika 
            where concat(a.datumPrikupa, \' \', d.imeprezime, \' \', e.serijskiKod,\'\')
            like :uvjet
        group by 
            a.sifra,a.datumPrikupa,d.imeprezime, e.serijskiKod  
        order by datumPrikupa asc
        limit :pocetak, :brps;

        ');
        $izraz->bindValue('pocetak',$pocetak, PDO::PARAM_INT); // param int tako da mi salje int a ne string
        $izraz->bindValue('brps',$brps, PDO::PARAM_INT); // param int tako da mi salje int a ne string
        $izraz->bindParam('uvjet', $uvjet);

        $izraz->execute();
        return $izraz->fetchAll();
    }

    public static function ukupnoPrikupa($uvjet='')
    {
        $uvjet = '%' . $uvjet . '%';
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select count(*)
        from prikup a 
            left join pacijent d on d.sifra = a.pacijent
            left join koncentratorKisika e on e.sifra = a.koncentratorKisika 
        where  
            concat(a.datumPrikupa, \' \', d.imeprezime, \' \', e.serijskiKod,\'\')
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
            a.datumPrikupa,
            b.sifra as pacijentSifra,
            b.imeprezime,
            c.sifra as kisikSifra,
            c.serijskiKod 
        from prikup a
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
        
        insert into prikup
        (datumPrikupa)
        values
        (:datumPrikupa);
        
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
            update prikup set
            datumPrikupa=:datumPrikupa
            where sifra=:sifra
        ');
        $izraz->execute($parametri);
    }

    public static function delete($sifra)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
            delete from prikup
            where sifra=:sifra
        
        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);
    }

    public static function noviPrikup()
{
    $veza = DB::getInstance();

    // get pacijent and koncentratorKisika from $_POST array
    $pacijent = $_POST['pacijent'];
    $koncentratorKisika = $_POST['koncentratoriKisika'];

    // update isporuka with flag = 0 for the minimum sifra where pacijent and koncentratorKisika match
    $izraz = $veza->prepare('
        UPDATE isporuka a
        SET flag = 0
        WHERE a.pacijent = :pacijent
        AND a.koncentratorKisika = :koncentratorKisika
        AND a.sifra = (
            SELECT MAX(b.sifra)
            FROM isporuka b
            WHERE b.pacijent = :pacijent
            AND b.koncentratorKisika = :koncentratorKisika
        )
    ');
    $izraz->execute([
        'pacijent' => $pacijent,
        'koncentratorKisika' => $koncentratorKisika
    ]);

    // insert a new record into prikup
    $izraz = $veza->prepare('
        INSERT INTO prikup 
            (datumPrikupa,pacijent,koncentratorKisika)
        VALUES 
            (:datumPrikupa,:pacijent,:koncentratorKisika)
    ');
    $izraz->execute([
        'datumPrikupa' => $_POST['datumPrikupa'],
        'pacijent' => $pacijent,
        'koncentratorKisika' => $koncentratorKisika
    ]);
}
    

    
    public static function azurirajPrikup($sifra)
    {

        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        update prikup set 
            datumPrikupa=:datumPrikupa, 
            pacijent=:pacijent, 
            koncentratorKisika=:koncentratorKisika 
        where sifra=:sifra
        ');
        $izraz->execute([
            'datumPrikupa'=>$_POST['datumPrikupa'],
            'pacijent'=>$_POST['pacijent'],
            'koncentratorKisika'=>$_POST['koncentratoriKisika'],
            'sifra' => $sifra
        ]);

    }

    
    public static function obrisiPacijentPrikup($pacijent)
    {   
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
            DELETE FROM prikup
            WHERE pacijent = :pacijent
        ');
        $izraz->execute([
            'pacijent' => $pacijent
        ]);
    }

    public static function obrisiKoncentratorKisikaPrikup( $koncentratorKisika)
    {   
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
           delete koncentratorKisika from prikup
           where koncentratorKisika=:koncentratorKisika
        
        ');
        $izraz->execute([
            
            'koncentratorKisika'=>$koncentratorKisika
        ]);
    }
}