

# C:\Users\Adam\xampp\mysql\bin> prvo ovo 
# mysql -uroot --default_character_set=utf8 < C:\Users\Adam\Desktop\repositories\Warevic\www\app.hr\warehouse.sql

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
    datumIsporuke date,
    pacijent int,
    koncentratorKisika int
);

create table prikup(
    sifra int not null primary key auto_increment,
    datumPrikupa date not null,
    pacijent int not null,
    koncentratorKisika int not null
);

#PROIZVOD
alter table isporuka add foreign key (koncentratorKisika) references koncentratorKisika (sifra);
alter table isporuka add foreign key (pacijent) references pacijent (sifra);
alter table prikup add foreign key (pacijent) references pacijent (sifra);
alter table prikup add foreign key (koncentratorKisika) references koncentratorKisika (sifra);




#--------------INSERT



insert into pacijent (sifra,imeprezime,telefon,datumRodenja,adresa,oib,pacijentKomentar)
values
(null,'Bubo Basić','097 6444789','1965.03.26','Ivaundulica 588','83500348060',''),
(null,'Ivo Mali','097 6444789','1990.03.26','Iva Guica 8','59452136896',''),
(null,'Bubo Basić14','097 6444789','1965.03.26','Ivaundulica 588','83500348060',''),
(null,'Ivo Mali13','097 6444789','1990.03.26','Iva Guica 8','59452136896',''),
(null,'Bubo Basić12','097 6444789','1965.03.26','Ivaundulica 588','83500348060',''),
(null,'Ivo Mali11','097 6444789','1990.03.26','Iva Guica 8','59452136896',''),
(null,'Bubo Basić0','097 6444789','1965.03.26','Ivaundulica 588','83500348060',''),
(null,'Ivo Mali9','097 6444789','1990.03.26','Iva Guica 8','59452136896',''),
(null,'Bubo Basić8','097 6444789','1965.03.26','Ivaundulica 588','83500348060',''),
(null,'Ivo Mali7','097 6444789','1990.03.26','Iva Guica 8','59452136896',''),
(null,'Bubo Basić6','097 6444789','1965.03.26','Ivaundulica 588','83500348060',''),
(null,'Ivo Mali5','097 6444789','1990.03.26','Iva Guica 8','59452136896',''),
(null,'Bubo Basić4','097 6444789','1965.03.26','Ivaundulica 588','83500348060',''),
(null,'Ivo Mali3','097 6444789','1990.03.26','Iva Guica 8','59452136896',''),
(null,'Bubo Basić2','097 6444789','1965.03.26','Ivaundulica 588','83500348060',''),
(null,'Ivo Mali1','097 6444789','1990.03.26','Iva Guica 8','59452136896','')
;


insert into koncentratorKisika(sifra,serijskikod,radniSat,proizvodac,
                                model,datumKupovine,ocKomentar)
values
(null,'BK533456343','124,5','Devilbiss','5L','2020.03.14',''),
(null,'BK345674343','2257','Devilbiss','5L','2020.02.19',''),
(null,'BK533451243','124,5','OXTM','5L','2020.03.14',''),
(null,'BK345672343','2257','Devilbiss','5L','2020.02.19',''),
(null,'BK533453443','124,5','Olive','5L','2020.03.14',''),
(null,'BK345674543','2257','Devilbiss','5L','2020.02.19',''),
(null,'BK533455643','124,5','Devilbiss','5L','2020.03.14',''),
(null,'BK345675643','2257','Olive','5L','2020.02.19',''),
(null,'BK533456743','124,5','Devilbiss','5L','2020.03.14',''),
(null,'BK345677843','2257','Philips','5L','2020.02.19',''),
(null,'BK533458943','124,5','Devilbiss','5L','2020.03.14',''),
(null,'BK345678993','2257','Philips','10L','2020.02.19',''),
(null,'BK111456343','124,5','Devilbiss','5L','2020.03.14',''),
(null,'BK222674343','2257','OXTM','5L','2020.02.19',''),
(null,'BK444456343','124,5','Devilbiss','10L','2020.03.14',''),
(null,'AK666674343','2257','Devilbiss','5L','2020.02.19',''),
(null,'AK533456343','124,5','Devilbiss','5L','2020.03.14',''),
(null,'AK345674343','2257','Devilbiss','5L','2020.02.19',''),
(null,'AK533451243','124,5','OXTM','5L','2020.03.14',''),
(null,'AK345672343','2257','Devilbiss','5L','2020.02.19',''),
(null,'AK533453443','124,5','Olive','5L','2020.03.14',''),
(null,'AK345674543','2257','Devilbiss','5L','2020.02.19',''),
(null,'AK533455643','124,5','Devilbiss','5L','2020.03.14',''),
(null,'AK345675643','2257','Olive','5L','2020.02.19',''),
(null,'AK533456743','124,5','Devilbiss','5L','2020.03.14',''),
(null,'AK345677843','2257','Philips','5L','2020.02.19',''),
(null,'AK533458943','124,5','Devilbiss','5L','2020.03.14',''),
(null,'AK345678993','2257','Philips','10L','2020.02.19',''),
(null,'AK111456343','124,5','Devilbiss','5L','2020.03.14',''),
(null,'AK222674343','2257','OXTM','5L','2020.02.19',''),
(null,'AK444456343','124,5','Devilbiss','10L','2020.03.14',''),
(null,'AK666674343','2257','Devilbiss','5L','2020.02.19','')
;

insert into isporuka(sifra,datumIsporuke,pacijent,koncentratorKisika)
values
(null,'2022.04.03',1,1),
(null,'2022.12.03',2,2);

insert into prikup(sifra,datumPrikupa,pacijent,koncentratorKisika)
values
(null,'2022.11.13',1,1),
(null,'2023.03.07',2,2);






