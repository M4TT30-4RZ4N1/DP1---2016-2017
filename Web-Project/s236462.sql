DROP TABLE IF EXISTS
    `testtable`;
DROP TABLE IF EXISTS
    `offer`;
    
-- tabella
CREATE TABLE `testtable`(
    `user` VARCHAR(50) NOT NULL,
    `password` VARCHAR(40) NOT NULL,
    `thr` double(7,2),
    `date` datetime

); 

CREATE TABLE `offer`(
    `user` VARCHAR(50) NOT NULL,
     `bid` double(7,2)
); 



-- valori
-- attenzione: nel sito ho usato assieme ad MD5 un secondo livello di criptazione della password che prevede l'inserimento di 4 caratteri all'inizio e 4 caratteri al fondo, tale tecnica permette di evitare un reverse hashing md5 delle password a chi ha accesso eventualmente al database.
INSERT INTO `testtable`(`user`, `password`, `thr`, `date`) VALUES
('a@p.it', CONCAT('x1y2',MD5('p1'),'z377'), NULL, NULL),
('b@p.it', CONCAT('ty42',MD5('p2'),'as6h'), NULL, NULL),
('c@p.it', CONCAT('cd59',MD5('p3'),'l03f'), NULL, NULL);

INSERT INTO`offer`(`user`, `bid`) VALUES('nessuno', 1);
