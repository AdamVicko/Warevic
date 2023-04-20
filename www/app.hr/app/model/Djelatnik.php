<?php

class Djelatnik // osoba koja moze mjenjati stanje koncentratora isporuka prikupa pacijenata
{

    public static function read($uvjet='',$stranica=1) //CRUD!
    {
        $uvjet = '%' . $uvjet . '%';
        $brps = App::config('brps');
        $pocetak = ($stranica * $brps) - $brps;

        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select
            sifra,imeprezime,telefon,email,lozinka,uloga
        from djelatnik
        where imeprezime like :uvjet
            group by sifra, 
            imeprezime ,
            telefon ,
            email ,
            lozinka ,
            uloga
        order by imeprezime asc limit :pocetak, :brps;
        
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
        
            select * from djelatnik
            where sifra=:sifra
        
        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);
        return $izraz->fetch();
    }

    public static function create($parametri)
    {
        $password = password_hash($_POST['lozinka'], PASSWORD_BCRYPT);
        $parametri['lozinka']= $password;
        
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        insert into djelatnik(imeprezime,telefon,email,lozinka,uloga)
        values(:imeprezime,:telefon,:email,:lozinka,:uloga);

        ');//dvotocke moraju odgovarat vrijednosti name od inputa
        $izraz->execute($parametri);
    }

    public static function update($parametri)
    {
        $veza = DB::getInstance();

        if (false === empty($_POST['lozinka'])) {
            $password = password_hash($_POST['lozinka'], PASSWORD_BCRYPT);

            $parametri['lozinka']= $password;

            $izraz = $veza->prepare('
        
            update djelatnik set
                lozinka=:lozinka
            where sifra=:sifra
    
            ');//dvotocke moraju odgovarat vrijednosti name od inputa

            $izraz->bindValue('lozinka',$parametri['lozinka']);
            $izraz->bindValue('sifra',$parametri['sifra']);
            $izraz->execute();
        } else {
            $izraz = $veza->prepare('
        
            UPDATE djelatnik SET
                imeprezime=:imeprezime,
                telefon =:telefon,
                email =:email,
                uloga =:uloga
            WHERE sifra=:sifra
    
            ');//dvotocke moraju odgovarat vrijednosti name od inputa
            $izraz->execute($parametri);
        }
    }

    public static function delete($sifra)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
            delete from djelatnik
            where sifra=:sifra
        
        ');
        $izraz->execute([
            'sifra'=>$sifra
        ]);
    }

    public static function ukupnoDjelatnika($uvjet='')
    {
        $uvjet = '%' . $uvjet . '%';
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select count(*)
        from 
        djelatnik a
        where a.imeprezime
        like :uvjet;
        
        ');
        $izraz->execute([
            'uvjet'=>$uvjet
        ]);
        return $izraz->fetchColumn();
    }

    public static function prviDjelatnik()
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
            select sifra from djelatnik
            order by sifra limit 1
        
        ');
        $izraz->execute();
        $sifra=$izraz->fetchColumn();
        return $sifra;
    }

    public static function autoriziraj($email,$password)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select * from djelatnik where email=:email
        
        ');
        $izraz->execute([
            'email'=>$email
        ]);

        $djelatnik = $izraz->fetch();

        if($djelatnik==null)
        {
            return null;
        }

        if(!password_verify($password,$djelatnik->lozinka))
        {
            return null;
        }

        unset($djelatnik->lozinka); // da ne spremi lozinku u session file na serveru
        
        return $djelatnik;
    }
}