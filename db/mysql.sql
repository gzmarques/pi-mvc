/* scripts para criação do banco de dados */

USE dw3_2016;

CREATE TABLE users (
  id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  cpf VARCHAR(11) UNIQUE NOT NULL,
  first_name VARCHAR(50) NOT NULL,
  last_name VARCHAR(50) NOT NULL,
  birth_date DATE,
  admission_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  firing_date DATETIME,
  email VARCHAR(255) UNIQUE NOT NULL,
  password CHAR(40) NOT NULL,
  total_sold DOUBLE NOT NULL DEFAULT 0,
  total_pending DOUBLE NOT NULL DEFAULT 0,
  permission_class TINYINT(1) NOT NULL DEFAULT 0
);

CREATE TABLE clients (
  id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  type TINYINT(1) NOT NULL DEFAULT 0,
  zip_code VARCHAR(16),
  street VARCHAR(50),
  num VARCHAR(4),
  city_id INT,

  CONSTRAINT FOREIGN KEY (city_id) REFERENCES cities(id)
);

CREATE TABLE natural_persons (
  id INT PRIMARY KEY NOT NULL,
  cpf VARCHAR(11) UNIQUE NOT NULL,
  first_name VARCHAR(50) NOT NULL,
  last_name VARCHAR(50),
  birth_date DATE,

  CONSTRAINT FOREIGN KEY (id) REFERENCES clients(id)
);

CREATE TABLE legal_persons (
  id INT PRIMARY KEY NOT NULL,
  cnpj CHAR(14) UNIQUE NOT NULL,
  legal_name VARCHAR(100),
  trade_name VARCHAR(100),

  CONSTRAINT FOREIGN KEY (id) REFERENCES clients(id)
);

-- CREATE TABLE clients (
--   id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
--   zip_code VARCHAR(16),
--   type TINYINT(1),
--   city_id INT,
--
--   cpf CHAR(11),
--   first_name VARCHAR(50),
--   last_name VARCHAR(50),
--   birth_date DATE,
--
--   cnpj CHAR(14),
--
--   CONSTRAINT FOREIGN KEY (city_id) REFERENCES cities(id)
-- );
--
-- CREATE TABLE natural_persons (
--   id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
--   cpf CHAR(11) NOT NULL,
--   first_name VARCHAR(50) NOT NULL,
--   last_name VARCHAR(50),
--   birth_date DATE,
--   city_id INT,
--
--   CONSTRAINT FOREIGN KEY (city_id) REFERENCES cities(id)
-- );
--
-- CREATE TABLE legal_persons (
--   id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
--   cnpj CHAR(14) NOT NULL,
--   legal_name VARCHAR(100) NOT NULL,
--   trade_name VARCHAR(100) NOT NULL,
--   city_id INT,
--
--   CONSTRAINT FOREIGN KEY (city_id) REFERENCES cities(id)
-- );

-- CREATE TABLE clients (
--   id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
--   type TINYINT(1),
--
--   cnpj CHAR(14),
--   legal_name VARCHAR(100),
--   trade_name VARCHAR(100),
--
--   cpf CHAR(11),
--   first_name VARCHAR(50),
--   last_name VARCHAR(50),
--   birth_date DATE,
--   city_id INT,
--
--   CONSTRAINT FOREIGN KEY (city_id) REFERENCES cities(id)
-- );

CREATE TABLE orders (
  id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status BOOL DEFAULT FALSE,
  total DOUBLE DEFAULT 0,
  closed_at DATETIME,
  user_id INT NOT NULL,
  client_id INT NOT NULL,

  CONSTRAINT FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT FOREIGN KEY (client_id) REFERENCES clients(id)
);

CREATE TABLE categories (
  id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
  id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  price DOUBLE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  stock INT DEFAULT 15,
  total_sold INT DEFAULT 0,
  total_pending INT DEFAULT 0,

  category_id INT NOT NULL,
  CONSTRAINT FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE item_order_products (
  id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  amount INT NOT NULL,
  item_price DOUBLE NOT NULL,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT FOREIGN KEY (product_id) REFERENCES products(id)
);

-- CREATE TABLE users (
--   id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
--   first_name VARCHAR(50) NOT NULL,
--   last_name VARCHAR(50) NOT NULL,
--   email VARCHAR(255) UNIQUE NOT NULL,
--   password VARCHAR(40) NOT NULL,
--   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );

-- CREATE TABLE users (
--   id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
--   first_name VARCHAR(50) NOT NULL,
--   last_name VARCHAR(50) NOT NULL,
--   middle_name VARCHAR(50),
--   birth_date DATE,
--   admission_date DATE NOT NULL,
--   firing_date DATE,
--   department_id INT,
--   email VARCHAR(255) UNIQUE NOT NULL,
--   password CHAR(40) NOT NULL,
--   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );

CREATE TABLE contacts (
  id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  email VARCHAR(50) NOT NULL,
  msg TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- INSERT INTO
--   users (first_name, last_name, email, password)
-- VALUES
--   ("Guilherme Zacalusni Marques", "gzmarques90@gmail.com", "62fbe97113baa78a7e2bab0f21b50ef525f6dc37");


INSERT INTO
  users (cpf, first_name, last_name, email, admission_date, password, permission_class)
VALUES
  ("07349515901", "Guilherme", "Zacalusni Marques", "gzmarques90@gmail.com", CURRENT_TIMESTAMP, "62fbe97113baa78a7e2bab0f21b50ef525f6dc37", 0),
  ("07532464903", "Pollyana", "Leschuk Zacalusni Marques", "pollyana.leschuk@gmail.com", CURRENT_TIMESTAMP, "62fbe97113baa78a7e2bab0f21b50ef525f6dc37", 0),
  ("10292018765", "Zenon", "Barriga Y Pesado", "srbarriga@tangamandapio.com.mx", CURRENT_TIMESTAMP, "62fbe97113baa78a7e2bab0f21b50ef525f6dc37", 0),
  ("28746352777", "Ramon", "Valdez", "chapeus_sapatos_ou_roupa_usada@quemtem.com.mx", CURRENT_TIMESTAMP, "62fbe97113baa78a7e2bab0f21b50ef525f6dc37", 0),
  ("84663890183", "Diego", "Marczal", "marczal@utfpr.edu.br", CURRENT_TIMESTAMP, "62fbe97113baa78a7e2bab0f21b50ef525f6dc37", 9),
  ("84950937891", "Paulo", "Henrique Soares", "paulosoares@utfpr.edu.br", CURRENT_TIMESTAMP, "62fbe97113baa78a7e2bab0f21b50ef525f6dc37", 9);

INSERT INTO
  clients (zip_code, type, city_id)
VALUES
  ('88888888', 0, 3456);
INSERT INTO
  natural_persons (id, cpf, first_name, last_name)
VALUES
  ((SELECT LAST_INSERT_ID()), '76840283958', 'Florinda', 'Corcuera y Vidialpango viúva de Matalascayano');

INSERT INTO
  clients (zip_code, type, city_id)
VALUES
  ('87654321', 0, 1234);
INSERT INTO
  natural_persons (id, cpf, first_name, last_name)
VALUES
  ((SELECT LAST_INSERT_ID()), '12345678910', 'Inocencio', 'Girafales');

INSERT INTO
  clients (zip_code, type, city_id)
VALUES
  ('94083029', 0, 2865);
INSERT INTO
  natural_persons (id, cpf, first_name, last_name)
VALUES
  ((SELECT LAST_INSERT_ID()), '74002959022', 'Frederico', 'Matalascayano y Corcuera');

INSERT INTO
  clients (zip_code, type, city_id)
VALUES
  ('56734500', 0, 12);
INSERT INTO
  natural_persons (id, cpf, first_name, last_name)
VALUES
  ((SELECT LAST_INSERT_ID()), '33618630098', 'Febronio', 'Barriga Gordorritúa');

INSERT INTO
  clients (zip_code, type, city_id)
VALUES
  ('10293848', 0, 56);
INSERT INTO
  natural_persons (id, cpf, first_name, last_name)
VALUES
  ((SELECT LAST_INSERT_ID()), '11238950837', 'Maria', 'Popisa');

INSERT INTO
  clients (zip_code, type, city_id)
VALUES
  ('85010970', 1, 2920);
INSERT INTO
  legal_persons (id, cnpj, trade_name, legal_name)
VALUES
  ((SELECT LAST_INSERT_ID()), '78206307000130', 'Empresa Fictícia', 'Razão Social Aqui LTDA.');

INSERT INTO
  clients (zip_code, type, city_id)
VALUES
  ('45321235', 1, 456);
INSERT INTO
  legal_persons (id, cnpj, trade_name, legal_name)
VALUES
  ((SELECT LAST_INSERT_ID()), '09347819476389', 'Escola da Vila', 'Instituição de Ensino Escolar do Oito');

INSERT INTO
  clients (zip_code, type, city_id)
VALUES
  ('67834521', 1, 2920);
INSERT INTO
  legal_persons (id, cnpj, trade_name, legal_name)
VALUES
  ((SELECT LAST_INSERT_ID()), '48500684758234', 'Churros Dona Florinda', 'Corcuera Y Vidialpango Comércio de Doces - MEI');

INSERT INTO
  clients (zip_code, type, city_id)
VALUES
  ('43256785', 1, 2178);
INSERT INTO
  legal_persons (id, cnpj, trade_name, legal_name)
VALUES
  ((SELECT LAST_INSERT_ID()), '75648938654815', 'Barriga Corretores', 'Barriga Y Pesado Aluguéis de Imóveis ME');

INSERT INTO
  clients (zip_code, type, city_id)
VALUES
  ('12309878', 1, 5300);
INSERT INTO
  legal_persons (id, cnpj, trade_name, legal_name)
VALUES
  ((SELECT LAST_INSERT_ID()), '59837660273612', 'Tienda del Chavo', 'Do Oito Sucos MEI');

INSERT INTO
  categories (name)
VALUES
  ("Smartphones"),
  ("Tablets"),
  ("Notebooks"),
  ("Monitores"),
  ("Acessórios");

INSERT INTO
  products (name, price, category_id)
VALUES
  ("Motorola Moto X XT1097", 1500, 1),
  ("Motorola Moto G XT1067", 900, 1),
  ("Samsung Galaxy S7", 3000, 1),
  ("Motorola Moto X Force", 3100, 1),
  ("Sony Xperia L", 400, 1),
  ("HTC One", 2000, 1),
  ("Nokia 3320", 3320, 1),
  ("Hiphone", 5, 1),
  ("Samsung Galaxy S3", 800, 1),
  ("LG G3 Flex", 498, 1);

INSERT INTO
  products (name, price, category_id)
VALUES
  ("Google HTC Nexus 9", 1300, 2),
  ("Samsung Galaxy Tab E", 800, 2),
  ("CCE Motion", 200, 2),
  ("Multilaser M7S", 250, 2),
  ("Motorola Xoom 2", 800, 2),
  ("Samsung T670", 2700, 2),
  ("Multilaser Supra", 251, 2),
  ("Apple iPad Air 2", 3699, 2),
  ("Apple iPad mini 2", 2300, 2),
  ("Nokia Lumia 2520", 1281.83, 2);

INSERT INTO
  products (name, price, category_id)
VALUES
  ("ASUS UL30VT-X1", 2000, 3),
  ("Apple MacBook Air", 5000, 3),
  ("Dell XPS13", 9000, 3),
  ("Samsung Chromebook 2", 1000, 3),
  ("Lenovo G40-80", 1700, 3),
  ("Positivo Premium XRI7150", 1400, 3),
  ("Alienware 15", 16878, 3),
  ("ASUS s46cb", 3000, 3),
  ("Acer Aspire ES1-431-C3W6", 1500, 3),
  ("Samsung Essentials E21", 1699.9, 3);

INSERT INTO
  products (name, price, category_id)
VALUES
  ("LED 25 polegadas LG 25UM57-P UltraWidescreen Full HD", 899.99, 4),
  ("LED 21,5 polegadas Samsung S22E310 Widescreen Full HD", 639.9, 4),
  ("LED 19,5 polegadas LG 20M37AA-B.AWZ", 396, 4),
  ("CRT 15 polegadas Dell E551C", 25, 4),
  ("LCD 18,5 polegadas HP L185B", 250, 4),
  ("CRT 17 polegadas LG T730sh", 60, 4),
  ("LED 23 polegadas Dell Professional P2314H", 802, 4),
  ("LCD 20 polegadas Samsung/Positivo Syncmaster 2043sw", 315, 4),
  ("LCD 17 polegadas Lenovo 4428", 229.9, 4),
  ("LED 25 polegadas LG 25UM57-P UltraWidescreen Full HD", 899, 4);

INSERT INTO
  products (name, price, category_id)
VALUES
  ("Carregador de celular para Motorola", 20, 5),
  ("Fones de ouvido Pioneer SE-M521", 100, 5),
  ("Cabo HDMI 1,5m", 30, 5),
  ("Capa para notebook 14 polegadas", 25, 5),
  ("Pendrive Kingston 32 Gb", 64.99, 5),
  ("Case para celular Samsung Galaxy S3", 14.99, 5),
  ("Teclado/Case para tablet 7 polegadas", 75.99, 5),
  ("Webcam 1.3 Mp", 30, 5),
  ("Mochila Trek para Notebook até 15,6 polegadas", 78.89, 5),
  ("Suporte de parede articulado para monitor", 88.98, 5);

-- INSERT INTO
--   orders (user_id, client_id, status, created_at)
-- VALUES
--   (6,1,0,'2016-04-25 08:33:21'),(4,8,0,'2016-04-26 09:45:13'),(3,6,0,'2016-04-27 10:11:12'),
--   (6,5,0,'2016-05-02 12:20:35'),(1,10,0,'2016-05-04 08:14:15'),(1,6,1,'2016-05-06 15:25:24'),
--   (3,4,1,'2016-05-09 14:55:01'),(4,7,1,'2016-05-10 16:08:55'),(1,5,0,'2016-05-13 13:13:13'),
--   (2,8,1,'2016-05-17 11:46:11'),(5,10,0,'2016-05-20 13:34:21'),(1,3,0,'2016-05-25 09:40:34'),
--   (5,9,1,'2016-05-30 17:33:21'),(5,3,0,'2016-06-03 18:33:21'),(4,9,1,'2016-06-10 11:33:21'),
--   (1,2,1,'2016-06-15 10:33:21'),(3,5,0,'2016-06-20 14:33:21'),(6,5,0,'2016-06-25 15:33:21');

  INSERT INTO
    orders (user_id, client_id, created_at)
  VALUES
    (6,1,'2016-04-25 08:33:21'),(4,8,'2016-04-26 09:45:13'),(3,6,'2016-04-27 10:11:12'),
    (6,5,'2016-05-02 12:20:35'),(1,10,'2016-05-04 08:14:15'),(1,6,'2016-05-06 15:25:24'),
    (3,4,'2016-05-09 14:55:01'),(4,7,'2016-05-10 16:08:55'),(1,5,'2016-05-13 13:13:13'),
    (2,8,'2016-05-17 11:46:11'),(5,10,'2016-05-20 13:34:21'),(1,3,'2016-05-25 09:40:34'),
    (5,9,'2016-05-30 17:33:21'),(5,3,'2016-06-03 18:33:21'),(4,9,'2016-06-10 11:33:21'),
    (1,2,'2016-06-15 10:33:21'),(3,5,'2016-06-20 14:33:21'),(6,5,'2016-06-25 15:33:21');

INSERT INTO
  item_order_products (order_id, product_id, amount, item_price)
VALUES
  (1, 3, 2, 3000), (1, 41, 1, 20),
  (2, 21, 1, 2000), (2, 50, 3, 88.98),
  (3, 3, 10, 3000), (3, 34, 5, 25), (3, 49, 1, 75),
  (4, 10, 2, 478), (4, 39, 1, 1600), (4, 33, 1, 9000),
  (5, 48, 15, 25), (5, 1, 4, 1500),
  (6, 50, 15, 85),
  (7, 43, 6, 30),
  (8, 27, 1, 16000), (8, 44, 12, 25),
  (9, 42, 2, 98), (9, 8, 3, 5),
  (10, 15, 4, 799), (10, 19, 1, 1200),
  (11, 39, 2, 229.9), (11, 42, 2, 30), (11, 50, 2, 88.98),
  (12, 13, 7, 200), (12, 42, 1, 98),
  (13, 26, 1, 700),
  (14, 47, 2, 75.99), (14, 16, 1, 1700), (14, 11, 1, 1300),
  (15, 36, 1, 60),
  (16, 4, 2, 3000), (16, 41, 2, 20);

UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=1) WHERE id=1;
UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=2) WHERE id=2;
UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=3) WHERE id=3;
UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=4) WHERE id=4;
UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=5) WHERE id=5;
UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=6) WHERE id=6;
UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=7) WHERE id=7;
UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=8) WHERE id=8;
UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=9) WHERE id=9;
UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=10) WHERE id=10;
UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=11) WHERE id=11;
UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=12) WHERE id=12;
UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=13) WHERE id=13;
UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=14) WHERE id=14;
UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=15) WHERE id=15;
UPDATE orders SET total=(SELECT SUM(item_price * amount) FROM item_order_products WHERE order_id=16) WHERE id=16;

UPDATE products SET total_pending=12 WHERE id=3;
UPDATE products SET total_pending=3 WHERE id=41;
UPDATE products SET total_pending=5 WHERE id=21;
UPDATE products SET total_pending=3 WHERE id=50;
UPDATE products SET total_pending=5 WHERE id=34;
UPDATE products SET total_pending=1 WHERE id=49;
UPDATE products SET total_pending=2 WHERE id=10;
UPDATE products SET total_pending=3 WHERE id=39;
UPDATE products SET total_pending=1 WHERE id=33;
UPDATE products SET total_pending=15 WHERE id=48;
UPDATE products SET total_pending=4 WHERE id=1;
UPDATE products SET total_pending=17 WHERE id=50;
UPDATE products SET total_pending=6 WHERE id=43;
UPDATE products SET total_pending=1 WHERE id=27;
UPDATE products SET total_pending=12 WHERE id=44;
UPDATE products SET total_pending=5 WHERE id=42;
UPDATE products SET total_pending=3 WHERE id=8;
UPDATE products SET total_pending=4 WHERE id=15;
UPDATE products SET total_pending=1 WHERE id=19;
UPDATE products SET total_pending=7 WHERE id=13;
UPDATE products SET total_pending=1 WHERE id=26;
UPDATE products SET total_pending=2 WHERE id=47;
UPDATE products SET total_pending=1 WHERE id=16;
UPDATE products SET total_pending=1 WHERE id=11;
UPDATE products SET total_pending=1 WHERE id=36;
UPDATE products SET total_pending=2 WHERE id=4;

UPDATE users SET total_pending=6000+40+1400+98+2*98+15+15*25+6000+15*85 WHERE id=1;
UPDATE users SET total_pending=4*799+1200 WHERE id=2;
UPDATE users SET total_pending=180+30000+5*25+75 WHERE id=3;
UPDATE users SET total_pending=2000+3*88.98+16000+12*25+60 WHERE id=4;
UPDATE users SET total_pending=2*229.9+60+2*88.98+700+2*75.99+1700+1300 WHERE id=5;
UPDATE users SET total_pending=6000+20+2*478+1600+9000 WHERE id=6;

-- UPDATE users SET total_sold=6000+40+1400+98+2*98+15+15*25+6000+15*85 WHERE id=1;
-- UPDATE users SET total_sold=4*799+1200 WHERE id=2;
-- UPDATE users SET total_sold=180+30000+5*25+75 WHERE id=3;
-- UPDATE users SET total_sold=2000+3*88.98+16000+12*25+60 WHERE id=4;
-- UPDATE users SET total_sold=2*229.9+60+2*88.98+700+2*75.99+1700+1300 WHERE id=5;
-- UPDATE users SET total_sold=6000+20+2*478+1600+9000 WHERE id=6;



-- 6(1,6,1,'2016-05-06 15:25:24'),14(1,2,1,'2016-06-15 10:33:21'),(14, 47, 2, 75.99), (14, 16, 1, 1700), (14, 11, 1, 1300),
-- 7(3,4,1,'2016-05-09 14:55:01'),
-- 8(4,7,1,'2016-05-10 16:08:55'),13(4,9,1,'2016-06-10 11:33:21'),
-- 10(2,8,1,'2016-05-17 11:46:11'),
-- 11(5,9,1,'2016-05-30 17:33:21'),



-- (1,2),(16, 4, 2, 3000), (16, 41, 2, 20);
-- (1,3),(12, 13, 7, 200), (12, 42, 1, 98),
-- (1,5),(9, 42, 2, 98), (9, 8, 3, 5),
-- (1,10),(5, 48, 15, 25), (5, 1, 4, 1500),
-- (1,6),(6, 50, 15, 85),
--
-- (2,8),(10, 15, 4, 799), (10, 19, 1, 1200),
--
-- (3,4),(7, 43, 6, 30),
-- (3,6),(3, 3, 10, 3000), (3, 34, 5, 25), (3, 49, 1, 75),
--
-- (4,8),(2, 21, 1, 2000), (2, 50, 3, 88.98),
-- (4,7),(8, 27, 1, 16000), (8, 44, 12, 25),
-- (4,9),(15, 36, 1, 60),
--
-- (5,10),(11, 39, 2, 229.9), (11, 42, 2, 30), (11, 50, 2, 88.98),
-- (5,9),(13, 26, 1, 700),
-- (5,3),(14, 47, 2, 75.99), (14, 16, 1, 1700), (14, 11, 1, 1300),
--
-- (6,1),(1, 3, 2, 3000), (1, 41, 1, 20),
-- (6,5),(4, 10, 2, 478), (4, 39, 1, 1600), (4, 33, 1, 9000),
--
--
--
--
--
--
--
--
-- (3,5),(6,5);
--
-- (1,2),(1,10),(1,6),(1,5),(1,3),
-- (2,8),
-- (3,5),(3,4),(3,6),
-- (4,7),(4,9),(4,8),
-- (5,9),(5,3),(5,10),
-- (6,5),(6,1),(6,5);
