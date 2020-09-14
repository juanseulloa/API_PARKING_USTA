drop table if exists LOGIN;

drop table if exists RATE;

drop table if exists SERVICE;

drop table if exists USERS;

/*==============================================================*/
/* Table: LOGIN                                                 */
/*==============================================================*/
create table LOGIN
(
    ID_LOGIN INT AUTO_INCREMENT,
    ID_USER INT(11) NOT NULL,
    PASSWORD VARCHAR(20) NOT NULL,
    CONSTRAINT PRIMARY KEY (ID_LOGIN)
);

/*==============================================================*/
/* Table: RATE                                                  */
/*==============================================================*/
create table RATE
(
    ID_RATE INT AUTO_INCREMENT,
    TIPE_RATE CHAR (10) NOT NULL,
    PRICE DECIMAL(10) NOT NULL,
    CONSTRAINT PRIMARY KEY (ID_RATE)
);

/*==============================================================*/
/* Table: SERVICE                                               */
/*==============================================================*/
create table SERVICE
(
    ID_SERVICE INT AUTO_INCREMENT,
    START_DATE DATE NOT NULL,
    END_DATE DATE NOT NULL,
    LICENSE_PLATE CHAR(6)NOT NULL,
    ID_USER INT (11) NOT NULL,
    ID_RATE  INT (11)NOT NULL,
    ID_LOGIN INT (11) NOT NULL,
    CONSTRAINT PRIMARY KEY (ID_SERVICE)
);

/*==============================================================*/
/* Table: USERS                                                 */
/*==============================================================*/
create table USERS
(
ID_USER INT AUTO_INCREMENT,
    NAME VARCHAR(200) NOT NULL,
    DOCUMENT VARCHAR (20) NOT NULL,
    TELEPHONE VARCHAR (10) NOT NULL,
    EMAIL VARCHAR (20) NOT NULL,
    ROL_USER  CHAR (7)NOT NULL,
    CONSTRAINT PRIMARY KEY (ID_USER)
);

   ALTER TABLE LOGIN
   ADD CONSTRAINT FK_LOGIN_USER FOREIGN KEY (ID_USER) REFERENCES USERS(ID_USER)ON UPDATE CASCADE;
   ALTER TABLE SERVICE
   ADD CONSTRAINT FK_SERVICE_USER FOREIGN KEY (ID_USER) REFERENCES USERS(ID_USER)ON UPDATE CASCADE,
   ADD CONSTRAINT FK_SERVICE_RATE FOREIGN KEY (ID_RATE) REFERENCES RATE(ID_RATE)ON UPDATE CASCADE,
   ADD CONSTRAINT FK_SERVICE_LOGIN FOREIGN KEY (ID_LOGIN) REFERENCES LOGIN(ID_LOGIN)ON UPDATE CASCADE;

INSERT INTO `users` (`ID_USER`, `NAME`, `DOCUMENT`, `EMAIL`, `ROL_USER`, `TELEPHONE`) VALUES (NULL, 'JUAN SEBASTIAN ULLOA', '1049656808', '*', 'Admin', '*'),
                                                                                             (NULL, 'PEPE PEREZ', '1010343654', 'pepe.perez@gmail.com', 'Special', '3102234432'),
                                                                                             (NULL, 'NATHALIA CASTILLO', '1010135395', '*', 'Admin', '*'),
                                                                                             (NULL, 'JOSE MARTNEZ', '10103345678', ' ', 'User', ' ');

INSERT INTO `login` (`ID_LOGIN`, `ID_USER`, `PASSWORD`) VALUES (NULL, '1', '1049656808'),
                                                               (NULL, '3', 'N1010135395C');

INSERT INTO `rate` (`ID_RATE`, `PRICE`, `TIPE_RATE`) VALUES (NULL, '25.000', 'MONT_MOT'),
                                                            (NULL, '1.000', 'DAY_MOT'),
                                                            (NULL, '50.000', 'MONT_CAR'),
                                                            (NULL, '3.000', 'DAY_CAR');

INSERT INTO `service` (`ID_SERVICE`, `START_DATE`, `ID_USER`, `ID_RATE`, `ID_LOGIN`, `LICENSE_PLATE`) VALUES (NULL, '2020-04-01', '2', '3', '2', 'HDF564'),
                                                                                                             (NULL, '2020-04-01', '4', '2', '1', 'HGF87I'),
                                                                                                             (NULL, '2020-04-03', '4', '2', '2', 'HGF87I'),
                                                                                                             (NULL, '2020-04-04', '4', '2', '1', 'HGF87I');
/*
 consultas
 */
SELECT u.name, u.TELEPHONE, u.EMAIL,TIMESTAMPDIFF(day, s.START_DATE, '2020-04-05') FROM users u INNER JOIN service s on u.ID_USER = s.ID_SERVICE WHERE date (s.START_DATE) BETWEEN '2020-04-1' and '2020-05-3' AND u.ROL_USER='Special';

SELECT u.name, u.TELEPHONE, u.EMAIL,TIMESTAMPDIFF(day, s.START_DATE, '2020-04-05'), s.START_DATE FROM users u INNER JOIN service s on u.ID_USER = s.ID_SERVICE WHERE date (s.START_DATE) BETWEEN '2020-02-29' and '2020-03-29' AND u.ROL_USER='Special';
/*
 consulta de busqueda de usuarios sin pagar
 */
 SELECT u.NAME,u.DOCUMENT,s.LICENSE_PLATE,s.START_DATE,s.END_DATE,TIMESTAMPDIFF(day, s.END_DATE, '2020-04-05') FROM service s INNER JOIN users u on s.ID_USER=u.ID_USER WHERE s.LICENSE_PLATE='HDF564' AND date (s.START_DATE) BETWEEN '2020-02-29' and '2020-03-29';

/*

 */
SELECT COUNT(u.DOCUMENT), u.DOCUMENT ,s.LICENSE_PLATE, s.START_DATE FROM users u INNER JOIN service s on u.ID_USER=s.ID_USER  WHERE date (s.START_DATE) BETWEEN '2020-04-1' and '2020-04-30' &&u.ROL_USER='User';

SELECT YEAR(S.END_DATE), MONTH(s.END_DATE), SUM(r.PRICE) FROM service S INNER JOIN rate r on s.ID_RATE=s.ID_RATE INNER JOIN users u on s.ID_USER=u.ID_USER WHERE u.ROL_USER='Special';

SELECT YEAR(s.START_DATE), MONTH(s.START_DATE),SUM(r.PRICE), r.TIPE_RATE FROM rate r INNER JOIN service s on r.ID_RATE=s.ID_RATE INNER JOIN users u on s.ID_USER=u.ID_USER WHERE s.START_DATE BETWEEN '2020-04-1'AND '2020-04-30' && u.ROL_USER='Special' GROUP BY r.TIPE_RATE;