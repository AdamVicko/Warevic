<?php


class DB extends PDO
{
    private static $instanca=null;
    private function __construct()
    {
        extract(App::config('baza')); // baza dolazi iz kofiguracije
        parent::__construct($dsn,$user,$password);
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_OBJ);  
    }

    public static function getInstance()
    {
        if(self::$instanca==null)
        {
            self::$instanca=new self();
        }
        return selF::$instanca;
    }
}