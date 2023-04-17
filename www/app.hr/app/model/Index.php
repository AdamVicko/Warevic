<?php


class Index 
{
    //CRUD OPERACIJE

    public static function searchAll($uvjet='')
    {

        $veza = DB::getInstance();
        $izraz = $veza->prepare('

        select sifra, \'pacijent\' as vrsta,
        imeprezime as tekst from pacijent
        where imeprezime like :uvjet
    union
        select sifra, \'koncentratorKisika\' as vrsta,
        serijskiKod as tekst from koncentratorKisika
        where serijskiKod like :uvjet
    union
        select 	a.sifra, \'prikup\' as vrsta,
        datumPrikupa as tekst
        from 
        prikup a 
        inner join pacijent b on a.pacijent=b.sifra
        inner join koncentratorKisika c on a.koncentratorKisika=c.sifra
        where concat( b.imeprezime, \' \', c.serijskiKod) like :uvjet
    union
        select 	a.sifra, \'isporuka\' as vrsta,
        datumIsporuke as tekst
        from 
        isporuka a 
        inner join pacijent b on a.pacijent=b.sifra
        inner join koncentratorKisika c on a.koncentratorKisika=c.sifra
        where concat( b.imeprezime, \' \', c.serijskiKod) like :uvjet;

        ');

        $izraz->execute(['uvjet'=>'%' . $uvjet . '%']);
        return $izraz->fetchAll(); 
    }
}