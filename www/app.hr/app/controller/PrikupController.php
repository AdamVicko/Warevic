<?php

class PrikupController extends AutorizacijaController
{

    private $viewPutanja = 'privatno'. 
    DIRECTORY_SEPARATOR . 'prikup' . 
    DIRECTORY_SEPARATOR;

    private $nf; // number formater dostupan u svim metodama ove klase 
    
    /*public function __construct()
    {
        parent::__construct(); // pozivam parent construct da ode provjerit u autorizacijacontroller dal ima ovlasti
        $this->nf = new NumberFormatter('hr-HR',NumberFormatter::DECIMAL); // format za prikaz broja(radni sat)
        $this->nf->setPattern('###,##0.00');
    }*/

    public function index()
    {
        $prikup = Prikup::read();
        /*foreach($prikup as $p)
        {
            if($p->radnisat==null)
            {
                $p->radnisat = $this->nf->format(0);
            }
            else
            {
                $p->radnisat = $this->nf->format($p->radnisat);
            }
        }*/

        $this->view->render($this->viewPutanja . 
        'index',
        [
            'podaci' => $prikup,
            'css' => 'prikup.css'
        ]);
    }

}