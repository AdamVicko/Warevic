<?php

class TestController 
{

    public function lozinka()
    {
        echo password_hash('edunova',PASSWORD_BCRYPT);
    }

}