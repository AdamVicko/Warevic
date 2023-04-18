<?php


class Index 
{
    //CRUD OPERACIJE

    public static function searchAll($uvjet='')
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('

        select sifra, \'Patient\' as vrsta,
        imeprezime as tekst from pacijent
        where imeprezime like :uvjet
    union
        select sifra, \'Oxygen Concentrator\' as vrsta,
        serijskiKod as tekst from koncentratorKisika
        where serijskiKod like :uvjet
    union
        select 	a.sifra, \'Collection\' as vrsta,
        datumPrikupa as tekst
        from 
        prikup a 
        inner join pacijent b on a.pacijent=b.sifra
        inner join koncentratorKisika c on a.koncentratorKisika=c.sifra
        where concat( b.imeprezime, \' \', c.serijskiKod) like :uvjet
    union
        select 	a.sifra, \'Delivery\' as vrsta,
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