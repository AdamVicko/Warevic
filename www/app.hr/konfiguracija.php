<?php
//lokalno
$dev = $_SERVER['SERVER_ADDR']==='127.0.0.1' ? true : false;//inline if ako je adresa 127.0.0.1 onda true


    if($dev)
    {
        return 
        [
            'dev'=>$dev,
            'url' => 'http://warevic.hr/',
            'nazivApp' => 'Warevic',
            'baza'=>
            [
                'dsn'=>'mysql:host=localhost;dbname=edunovapp26;charset=utf8mb4',
                'user'=>'root',
                'password'=>''
            ]
        ];
    }
    else
    {
        return
        [
            'dev'=>$dev,
            'url' => 'https://polaznik26.edunova.hr/',
            'nazivApp' => 'Warevic',
            'baza'=>
            [
                'dsn'=>'mysql:host=localhost;dbname=nika_edunovapp26;charset=utf8mb4',
                'user'=>'nika_edunova',
                'password'=>'Nika90876.!df'
            ]
        ];
       
    }
    


/*cpanel
return [
    'url' => 'https://www.polaznik26.edunova.hr/',
    'nazivApp' => 'Warevic',
    'baza'=>
    [
        'dsn'=>'mysql:host=localhost;dbname=edunovapp26;charset=utf8mb4',
        'user'=>'nika',
        'password'=>'Nika90876'
    ]
];
*/