drop database GH;
create database GH;
use GH;
show tables;
ALTER TABLE manager
ADD COLUMN Password VARCHAR(255) NOT NULL UNIQUE,
ADD COLUMN Gender ENUM('male', 'female') NOT NULL;


CREATE TABLE Manager (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Name_Manager VARCHAR(32) NOT NULL,
	Gender VARCHAR(16) NOT NULL,
    Email VARCHAR(255) NOT NULL ,
	Password VARCHAR(32) default 123456789,
    Manager_Address VARCHAR(255) NOT NULL,
    Age INT NOT NULL,
    Salary real NOT NULL,
    status varchar(16) DEFAULT 'active',
    Phone VARCHAR(20) NOT NULL
);



select * from Manager;
DELETE FROM Manager WHERE ID = 1;
SELECT ID, Name_Manager FROM manager WHERE status = 'active';
DROP TABLE GH.Full_time;
DROP TABLE GH.Part_time;
DROP TABLE GH.Employee;
select * from Employee;
select * from Employee;




CREATE TABLE Employee (
    E_ID INT PRIMARY KEY AUTO_INCREMENT,
    Name_Emp VARCHAR(16),
    gender VARCHAR(16),
    HireDate DATE,
    phone varchar(16),
    status varchar(16) DEFAULT 'active',
    Email  varchar(32),
    password_cus varchar(32) default '123456789',
    ID_Manager INT NOT NULL,
    FOREIGN KEY (ID_Manager) REFERENCES Manager(ID)
);


select * from manager;



create table Full_time(
E_ID int primary key,
salary real ,
foreign key (E_ID) references Employee (E_ID) ON DELETE CASCADE
);
create table Part_time(
E_ID int primary key,
Hour_rate int ,
price_hour real,
foreign key (E_ID) references Employee (E_ID) ON DELETE CASCADE
);


 SELECT 
    E.Name_Emp AS name, 
    F.salary AS full_time_salary,
    NULL AS part_time_total_payment
FROM Employee E
JOIN Full_time F ON E.E_ID = F.E_ID
WHERE E.status = 'active'

UNION

SELECT 
    E.Name_Emp AS name, 
    NULL AS full_time_salary,
    P.price_hour * P.Hour_rate AS part_time_total_payment
FROM Employee E
JOIN Part_time P ON E.E_ID = P.E_ID
WHERE E.status = 'active';
delete  from Employee E where E.E_ID = 2;
select * FROM Employee;
show tables;
select * from Full_time;
select * from Part_time;

 SELECT 
        'Full Time' AS category,
        (COUNT(F.E_ID) / (SELECT COUNT(E_ID) FROM Employee WHERE status = 'active') * 100) AS percentage
    FROM Employee E
    JOIN Full_time F ON E.E_ID = F.E_ID
    WHERE E.status = 'active';


create table Supplier(
ID_Sup int primary key auto_increment,
Name_Sup varchar(16),
Email varchar(32),
phone varchar(16),
status ENUM('active', 'inactive') DEFAULT 'active'
);
select * from Product;

INSERT INTO Supplier (Name_Sup, Email, phone)
VALUES ('Ahmad', 'supplier@example.com', 1234567890);
INSERT INTO Supplier (Name_Sup, Email, phone)
VALUES ('jimi', 'supplier@example.com', 1234567890);


create table Customer(
ID_cus int primary key auto_increment,
Address varchar(32),
phone int
);

show tables;

CREATE TABLE Orders (
    ID_order INT PRIMARY KEY AUTO_INCREMENT,
	order_Date date 
);
INSERT INTO Orders (order_Date) VALUES 
('2024-12-20');
INSERT INTO Orders (order_Date) VALUES 
('2024-12-21');
INSERT INTO Orders (order_Date) VALUES 
('2024-12-22');
INSERT INTO Orders (order_Date) VALUES 
('2024-12-23');
INSERT INTO Orders (order_Date) VALUES 
('2024-12-24');
INSERT INTO Orders (order_Date) VALUES 
('2024-12-25');
INSERT INTO Orders (order_Date) VALUES 
('2024-12-26');
INSERT INTO Orders (order_Date) VALUES 
('2024-12-27');
SELECT * FROM Orders WHERE order_Date = '2024-12-01';
SELECT * FROM order_line;
SELECT * FROM Orders;


INSERT INTO Product (Name_product, price, color, material, discount, Stock) VALUES
('Laptop', 1000.00, 'Silver', 'Metal', 10, 50),
('Smartphone', 800.00, 'Black', 'Plastic', 5, 100),
('Tablet', 600.00, 'White', 'Aluminum', 15, 30),
('Headphones', 200.00, 'Red', 'Plastic', 20, 150);
 select * from Product;


INSERT INTO order_line (ID_order, ID_P, quantity, unit_price, discount, total_price) VALUES
(1, 1, 1, 42.00, 0, 42.00),
(1, 2, 4, 28.80, 5, 109.44),
(1, 3, 5, 49.00, 3, 237.65),
(1, 4, 10, 71.28, 3, 691.416),
(9, 1, 2, 42.00, 1, 83.16),
(2, 5, 3, 90.16, 2, 88.3568),
(3, 4, 2, 71.28, 1, 141.1344),
(10, 4, 2, 71.28, 2, 139.7088);

select * from Product;
select * from Orders;
select * from order_line;
select * from Import_product;
select * from supplier;

drop table GH.Orders;
drop table GH.Product;
drop table GH.order_line;
drop table GH.Import_product;
drop table GH.supplier;

CREATE TABLE Product(
    ID_P INT PRIMARY KEY AUTO_INCREMENT,
    Name_product VARCHAR(32),
    price REAL NOT NULL,
    Import_price REAL NOT NULL, -- سعر الاستيراد
    Profit_margin REAL NOT NULL, -- نسبة الربح
    color VARCHAR(16),
    material VARCHAR(16),
    discount INT DEFAULT 0,
    Stock INT NOT NULL,
    category VARCHAR(100),                         
    image_url VARCHAR(255)
);
select * from Product;
select * from supplier;


CREATE TABLE order_line (
    ID_order INT NOT NULL,
    ID_P INT NOT NULL,
    quantity INT,
    unit_price INT NOT NULL,
    discount INT DEFAULT 0,
    total_price INT NOT NULL,
    PRIMARY KEY (ID_P, ID_order),
    FOREIGN KEY (ID_P) REFERENCES Product (ID_P),
    FOREIGN KEY (ID_order) REFERENCES Orders (ID_order)
);
create table sales_process(
ID_order int not null,
ID_cus int not null,
E_ID int not null,
date_sale date not null,
primary key (E_ID,ID_order,ID_cus),
foreign key (ID_cus) references Customer (ID_cus),
foreign key (ID_order) references Orders (ID_order),
foreign key (E_ID) references Employee (E_ID)
);
select * from Import_product;
drop table GH.Import_product;
drop table GH.supplier;
select * from manager;

UPDATE Product
SET material = 'qotton'
WHERE ID_P IN (2, 4);

create table Import_product(
ID_Man int  not null default 1,
ID_Sup int not null,
ID_P int not null,
primary key (ID_Man,ID_Sup,ID_P),
foreign key (ID_Man) references Manager (ID),
foreign key (ID_Sup) references Supplier (ID_Sup),
foreign key (ID_P) references Product (ID_P)  ON DELETE CASCADE
);
show tables;
