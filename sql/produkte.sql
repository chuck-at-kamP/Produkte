-- Erstelle DB + Tabellen (MariaDB)
CREATE DATABASE IF NOT EXISTS produkte CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE produkte;

CREATE TABLE IF NOT EXISTS items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  user_id INT NULL,
  short VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  image VARCHAR(255) NOT NULL,
  barcode VARCHAR(255) UNIQUE,

  price DECIMAL(10, 2) NOT NULL,
  cost DECIMAL(10, 2),
  stock INT NOT NULL,
  sold INT NULL,

  location_id INT NULL,
  category ENUM('Kleidung','Kinder','Wohnen','Schlafen','Haushalt','Kleinmöbel','Elektro'),

  subcategory VARCHAR(255),
  gender ENUM('Nicht relevant','Herren','Damen','Kinder','Sonstiges'),
  state ENUM('sehr gut','gut','mittel','schlecht','sehr schlecht'),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS location (
	id INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(255) NOT NULL,
	barcode VARCHAR(255) NULL,
	shelf INT NULL,
	pos VARCHAR(10) NULL,
	department ENUM('Kleidung','Kinder','Wohnen','Schlafen','Haushalt','Kleinmöbel','Elektro')
);

