<?php

class Operater
{
    public static function autoriziraj($email,$password)
    {
        $veza = DB::getInstance();
        $izraz = $veza->prepare('
        
        select * from operater where email=:email
        
        ');
        $izraz->execute([
            'email'=>$email
        ]);

        $operater = $izraz->fetch();

        if($operater==null)
        {
            return null;
        }

        if(!password_verify($password,$operater->lozinka))
        {
            return null;
        }

        unset($operater->lozinka); // da ne spremi lozinku u session file na serveru
        
        return $operater;
    }
}