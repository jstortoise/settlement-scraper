
# Settlement Data Scraper

This is a PHP-based web scraper for gathering settlement data from a specified URL and storing it in a MySQL database. The project includes functionalities for creating the database, scraping settlement data, and managing the data (insert, read, delete, drop table).

## Project Structure

```
/your-project-root
|-- index.php           # Main script to handle actions (create table, scrape data, etc.)
|-- /Model
|   |-- Model.php       # Base model class for database interactions
|   |-- Settlement.php  # Settlement model for dealing with settlement data
|-- /Service
|   |-- Scraper.php     # Class for scraping data from the URL
|-- /vendor             # Composer dependencies
|-- composer.json       # Composer configuration file
```

## Prerequisites

- PHP 7.4 or higher
- MySQL server
- Composer (for managing PHP dependencies)

## Installation Guide

### 1. Clone the repository

Clone the project to your local machine or server:

```bash
git clone https://github.com/jstortoise/settlement-scraper.git
cd settlement-scraper
```

### 2. Install dependencies

Make sure Composer is installed, then run the following command to install dependencies:

```bash
composer install
```

This will install the necessary libraries and dependencies, such as `PDO` for database interaction.

### 3. Set up the database

Make sure you have MySQL installed and running. You need to create a database for this project. You can do so by executing the following MySQL query:

```sql
CREATE DATABASE lk_test_db;
```

### 4. Configure Database Credentials

Ensure your MySQL credentials are correctly set up in `Model/Model.php`:

```php
$this->pdo = new PDO("mysql:host=localhost;dbname=lk_test_db;charset=utf8mb4", 'root', '');
```

You may need to change `'localhost'`, `'root'`, and `''` (password) according to your database configuration.

### 5. Access the Application

Once everything is set up, you can run the application by opening the `index.php` file in a web browser or through a local server (e.g., using `php -S localhost:8000`).

---

## How to Use the Application

Once the project is set up and running, you can interact with it using a web interface. The following actions are supported:

1. **Create the Database Table**

   If the database table doesn't exist, you can create it by clicking the "Create Database Table" button. This will set up the necessary table to store settlement data.

2. **Scrape Data**

   After creating the table, you can scrape settlement data by clicking the "Scrape Data" button. This will fetch data from the URL `https://zlk.com/settlement` and insert it into the database.

3. **Delete Data**

   You can delete all records from the table by clicking the "Delete Data" button.

4. **Drop the Table**

   If you want to remove the table from the database, click the "Drop Table" button. This will delete the entire table.

---

## MySQL Queries for Database Setup

You can create the database and tables manually by running the following SQL queries in your MySQL client or admin tool (e.g., phpMyAdmin, MySQL Workbench).

### 1. Create Database

```sql
CREATE DATABASE lk_test_db;
```

### 2. Create Table for Settlements

```sql
CREATE TABLE IF NOT EXISTS settlements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255),
    ticker_symbol VARCHAR(50),
    deadline DATETIME,
    class_period TEXT,
    settlement_fund VARCHAR(255),
    settlement_hearing_date VARCHAR(255),
    post_url TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## Notes

- **Scraping Limitations**: The scraper uses `file_get_contents()` to fetch the HTML content of the target page. Make sure the target URL (`https://zlk.com/settlement`) allows scraping, and that no anti-scraping measures (e.g., CAPTCHA, IP blocking) are in place.
- **Date Handling**: The scraper processes date fields (`Deadline` and `Hearing Date`) and formats them as `Y-m-d`.
- **Error Handling**: Errors in scraping and database operations will be shown on the web page for debugging purposes.
- **Field Exception**: Can't find the data to scrape the field of `Ticker Symbol` from the provided url.