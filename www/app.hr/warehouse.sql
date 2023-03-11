

# C:\Users\Adam\xampp\mysql\bin>mysql -uroot --default_character_set=utf8 < C:\Users\Adam\Desktop\repositories\Warevic\www\app.hr\warehouse.sql

drop database if exists edunovapp26;
create database edunovapp26 default charset utf8mb4;
use edunovapp26;

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
    oib char(11)
);

create table koncentratorKisika(
    sifra int not null primary key auto_increment,
    serijskiKod varchar(50),
    radnisat decimal(18,2),
    naziv varchar(50),
    proizvodac varchar(50),
    model varchar(50),
    komentar varchar(100),
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

create table stanje(
    sifra int not null primary key auto_increment,
    kolicinaKom int,
    koncentratorKisika int
);

#PROIZVOD
alter table stanje add foreign key (koncentratorKisika) references koncentratorKisika (sifra);
alter table isporuka add foreign key (koncentratorKisika) references koncentratorKisika (sifra);
alter table isporuka add foreign key (pacijent) references pacijent (sifra);
alter table prikup add foreign key (pacijent) references pacijent (sifra);
alter table prikup add foreign key (koncentratorKisika) references koncentratorKisika (sifra);




#--------------INSERT



insert into pacijent (sifra,imeprezime,telefon,datumRodenja,adresa,oib)
values
(null,'Ivo Andrić','097 6444789','1985.03.26','Ivana Gundulica 658','59452136897'),
(null,'Bubo Basić','097 6444789','1965.03.26','Ivaundulica 588','59452136197'),
(null,'Ivan And','097 6444789','1955.03.26','IvGundulica 598','59452136847'),
(null,'Ivana Andrić','097 6444789','1995.03.26','Ivana 588','59452136894'),
(null,'Mario rić','097 6444789','1988.03.26','Ivana dulica 582','59452636897'),
(null,'Muhammed ić','097 6444789','1981.03.26','Iva Gunica 583','59452936897'),
(null,'Nahid Kulenovic','097 6444789','1975.03.26','ana dulica 2538','51452136897'),
(null,'Nijaz Batlak','097 6444789','1973.03.26','Ia ndulica 583','59454136897'),
(null,'Semso Poplava','097 6444789','1989.03.26','vana Guca 558','59457136897'),
(null,'Ivo Mali','097 6444789','1990.03.26','Iva Guica 8','59452136896');


insert into koncentratorKisika(sifra,serijskikod,radniSat,naziv,proizvodac,
                                model,datumKupovine)
values
(null,'BK533456343','124,5','Koncentrator_Kisika','Devilbiss','5L','2020.03.14'),
(null,'BK345674343','14,5','Koncentrator_Kisika','Devilbiss','5L','2020.02.19'),
(null,'BK345746343','12','Koncentrator_Kisika','Devilbiss','5L','2020.01.29'),
(null,'BK3464566343','925','Koncentrator_Kisika','Devilbiss','5L','2020.07.21'),
(null,'BK3456343','5874','Koncentrator_Kisika','Devilbiss','5L','2020.04.10'),
(null,'BK5353456343','0','Koncentrator_Kisika','Devilbiss','5L','2020.11.02'),
(null,'BK30673456343','3655','Koncentrator_Kisika','Devilbiss','5L','2020.01.10'),
(null,'BK3456304003','14526','Koncentrator_Kisika','Devilbiss','5L','2020.04.13'),
(null,'BK34563943','124','Koncentrator_Kisika','Devilbiss','5L','2020.2.22'),
(null,'BK53456343','124,5','Koncentrator_Kisika','Devilbiss','5L','2020.03.14'),
(null,'BK674343','14,5','Koncentrator_Kisika','Devilbiss','5L','2020.02.19'),
(null,'GK746343','12','Koncentrator_Kisika','Devilbiss','5L','2020.01.29'),
(null,'GK64566343','925','Koncentrator_Kisika','Devilbiss','5L','2020.07.21'),
(null,'GK3456343','5874','Koncentrator_Kisika','Devilbiss','5L','2020.04.10'),
(null,'GK6343','0','Koncentrator_Kisika','Devilbiss','5L','2020.11.02'),
(null,'GK3056343','3655','Koncentrator_Kisika','Devilbiss','5L','2020.01.10'),
(null,'UZ56304003','14526','Koncentrator_Kisika','Devilbiss','5L','2020.04.13'),
(null,'UZ363943','124','Koncentrator_Kisika','Devilbiss','5L','2020.2.22')
;

insert into isporuka(sifra,datumIsporuke,pacijent,koncentratorKisika)
values
(null,'2022.04.03',1,3),
(null,'2022.12.03',2,2),
(null,'2022.02.08',3,1),
(null,'2022.01.11',4,4),
(null,'2022.10.07',5,5),
(null,'2022.04.01',6,6);



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





