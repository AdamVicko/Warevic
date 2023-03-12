<?php


class Prikup 
{
    //CRUD OPERACIJE

    public static function read()
    {

        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select a.datumPrikupa, b.imeprezime , c.serijskiKod 
        from prikup a 
        inner join pacijent b on a.pacijent = b.sifra 
        inner join koncentratorkisika c on a.koncentratorKisika = c.sifra 
        order by datumPrikupa desc;

        ');
        $izraz->execute();
        return $izraz->fetchAll();
    }
}