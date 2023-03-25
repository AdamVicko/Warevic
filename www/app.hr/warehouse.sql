

# C:\Users\Adam\xampp\mysql\bin>mysql -uroot --default_character_set=utf8 < C:\Users\Adam\Desktop\repositories\Warevic\www\app.hr\warehouse.sql

drop database if exists edunovapp26;
create database edunovapp26 default charset utf8mb4;
use edunovapp26;


#samo za cpanel 
#alter database nika_edunovapp26 charset utf8mb4;

# TABLICE

create table djelatnik (
    sifra int not null primary key auto_increment,
    imeprezime varchar(50) not null,
    telefon varchar (50) not null,
    email varchar(50) not null,
    lozinka char(61) not null, /*stavljam char 61 jer je encoder lozinke dug 61 character i vujek ce bit */
    uloga varchar(20) not null
);

insert into djelatnik (imeprezime,telefon,email,lozinka,uloga)
values ('Proba Operater','0945645645','proba@gmail.com',
    '$2y$10$Z41mPstzZZKpx0A1/g6g0OoQ43IyUZwqF/fcaHBipdQNdHlXQ4lR.',
    'operater'),
    ('Admin Operater','0945677645','admin@gmail.com',
    '$2y$10$Z41mPstzZZKpx0A1/g6g0OoQ43IyUZwqF/fcaHBipdQNdHlXQ4lR.',
    'administrator');

create table pacijent (
    sifra int not null primary key auto_increment,
    imeprezime varchar(50) not null,
    telefon varchar (50),
    datumRodenja date,
    adresa text(50),
    oib char(11),
    pacijentKomentar varchar(100)
);

create table koncentratorKisika(
    sifra int not null primary key auto_increment,
    serijskiKod varchar(50) not null,
    radniSat decimal(18,2),
    proizvodac varchar(50),
    model varchar(50),
    ocKomentar varchar(100),
    datumKupovine date
);

create table isporuka(
    sifra int not null primary key auto_increment,
    datumIsporuke date not null,
    pacijent int not null,
    koncentratorKisika int not null
);

create table prikup(
    sifra int not null primary key auto_increment,
    datumPrikupa date not null,
    pacijent int not null,
    koncentratorKisika int not null
);

/*create table stanje(
    sifra int not null primary key auto_increment,
  kolicinaKom int,
  koncentratorKisika int
);*/

#PROIZVOD
--alter table stanje add foreign key (koncentratorKisika) references koncentratorKisika (sifra);
alter table isporuka add foreign key (koncentratorKisika) references koncentratorKisika (sifra);
alter table isporuka add foreign key (pacijent) references pacijent (sifra);
alter table prikup add foreign key (pacijent) references pacijent (sifra);
alter table prikup add foreign key (koncentratorKisika) references koncentratorKisika (sifra);




#--------------INSERT



insert into pacijent (sifra,imeprezime,telefon,datumRodenja,adresa,oib,pacijentKomentar)
values
(null,'Bubo Basić','097 6444789','1965.03.26','Ivaundulica 588','83500348060',''),
(null,'Ivo Mali','097 6444789','1990.03.26','Iva Guica 8','59452136896','');


insert into koncentratorKisika(sifra,serijskikod,radniSat,proizvodac,
                                model,datumKupovine,ocKomentar)
values
(null,'BK533456343','124,5','Devilbiss','5L','2020.03.14',''),
(null,'BK345674343','2257','Devilbiss','5L','2020.02.19','');

insert into isporuka(sifra,datumIsporuke,pacijent,koncentratorKisika)
values
(null,'2022.04.03',1,1),
(null,'2022.12.03',2,2);

insert into prikup(sifra,datumPrikupa,pacijent,koncentratorKisika)
values
(null,'2022.11.13',1,1),
(null,'2023.03.07',2,2);


/*
insert into stanje (sifra,koncentratorKisika,kolicinaKom)
values
(null,1,null),
(null,2,null),
(null,3,null),
(null,4,null),
(null,5,null),
(null,6,null),
(null,7,null),
(null,8,null),
(null,9,null),
(null,10,null),
(null,11,null),
(null,12,null),
(null,13,null),
(null,14,null),
(null,15,null),
(null,16,null),
(null,17,null),
(null,18,null)
;
*/




