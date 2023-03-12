<?php

class Djelatnik // osoba koja moze mjenjati stanje koncentratora isporuka prikupa pacijenata
{

    public static function read() //CRUD!
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select * from djelatnik
        
        ');
        $izraz->execute();
        return $izraz->fetchAll();
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