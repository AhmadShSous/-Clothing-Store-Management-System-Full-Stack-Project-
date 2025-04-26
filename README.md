
# Clothing Store Management System (Full Stack Project)

## Project Overview
This project is a **Full Stack Web Application** designed to manage a clothing store, providing complete functionality for managers, employees, products, orders, and suppliers.

The system enables:
- **Manager Management** (add, update, delete managers)
- **Employee Management** (handle employee records)
- **Product Management** (manage inventory, discounts, suppliers)
- **Order Management** (create, track, and manage orders)
- **Supplier Management** (add and remove suppliers)
- **Statistical Analysis** (dynamic dashboards and charts)

Developed collaboratively by a team of three members.  
**Role:** Manager (Led the Manager module, contributed to overall system design and database structure).

---

## Features
- **Full CRUD Operations** for managers, employees, products, suppliers, and orders.
- **Dynamic Dashboards** displaying:
  - Product stock levels
  - Profit and revenue analysis
  - Employee statistics
  - Order tracking
- **Search and Filter** functionalities.
- **Authentication and Roles** for managing access.
- **Responsive UI Design** for desktop and mobile devices.
- **Live Charts** using **Chart.js**.
- **Real-time Updates** through PHP-MySQL integration.

---

## Technologies Used
- **Frontend**: 
  - HTML5, CSS3, JavaScript (vanilla)
- **Backend**: 
  - PHP
- **Database**: 
  - MySQL
- **Visualization**: 
  - Chart.js library
- **Deployment Environment**:
  - Localhost (XAMPP/WAMP stack)

---

## Folder Structure
- `/products/` → Manage products (Add, Update, Delete, Stats)
- `/employees/` → Manage employees (Add, Update, Delete, Stats)
- `/orders/` → Manage customer orders and view order statistics
- `/managers/` → Manage manager accounts and roles
- `/suppliers/` → Manage suppliers (Add and Delete)
- `/assets/` → CSS and JS files for design and interactivity
- `index.html` → Manager main dashboard

---


# Database Schema for Clothing Store Management System

This folder contains the SQL script needed to set up the database for the Clothing Store Management System project.

## Contents
- **ClothingStoreDB.sql**: SQL script to create the database schema, tables, and relationships.

## Instructions
1. Open your MySQL server (e.g., using XAMPP, WAMP, phpMyAdmin).
2. Run the script `ClothingStoreDB.sql` to create the `GH` database and all required tables.
3. The database includes tables for Managers, Employees, Products, Orders, Customers, Suppliers, and associated relationships.

## Important Notes
- Ensure that your MySQL server is running.
- This script will **DROP** and **CREATE** the `GH` database, so use it carefully if you already have a database with the same name.


## How to Run the Project
1. Install **XAMPP** or **WAMP** server.
2. Clone or download the repository into the `htdocs` directory.
3. Import the MySQL database from the provided `.sql` file (if available).
4. Start **Apache** and **MySQL** services from the control panel.
5. Open your browser and go to:
   ```
   http://localhost/index.html
   ```
6. Start managing the store using the dashboard.

---

## Example Screenshots
> (Optional: You can add screenshots showing dashboard, management pages, charts, etc.)

---

## Author
- **Ahmad Shaher Ahmad Sous**  
  Student ID: 1221371  
  Section: 4
