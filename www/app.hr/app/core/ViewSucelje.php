<?php

interface ViewSucelje
{
    public function index();
    public function novi();
    public function promjena($sifra=0);
    public function izbrisi($sifra=0);
    public function pocetniPodaci();

}