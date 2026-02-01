-- =========================================
-- Bookstore schema (MySQL 8+)
-- =========================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- (Si tu BD ya existe y haces USE en otro sitio, comenta estas 2 líneas)
DROP DATABASE IF EXISTS bookstore;

CREATE DATABASE IF NOT EXISTS bookstore
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE bookstore;

-- =========================================
-- Drop tables (safe reset)
-- =========================================
DROP TABLE IF EXISTS pack_item;
DROP TABLE IF EXISTS pack;
DROP TABLE IF EXISTS rating;
DROP TABLE IF EXISTS wishlist_item;
DROP TABLE IF EXISTS cart_item;
DROP TABLE IF EXISTS cart;
DROP TABLE IF EXISTS user_role;
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS role;
DROP TABLE IF EXISTS book_work_author;
DROP TABLE IF EXISTS book;
DROP TABLE IF EXISTS book_work;
DROP TABLE IF EXISTS product_category;
DROP TABLE IF EXISTS product;
DROP TABLE IF EXISTS author;
DROP TABLE IF EXISTS editorial;
DROP TABLE IF EXISTS category;


-- =========================================
-- Base tables
-- =========================================

CREATE TABLE IF NOT EXISTS category (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_category_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS editorial (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_editorial_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS author (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_author_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS role (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_role_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `user` (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  email VARCHAR(120) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_user_username (username),
  UNIQUE KEY uq_user_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- Product catalog
-- =========================================

CREATE TABLE IF NOT EXISTS product (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(200) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  image_path VARCHAR(255) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT chk_product_price_nonneg CHECK (price >= 0),
  CONSTRAINT chk_product_stock_nonneg CHECK (stock >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- N:M product <-> category
CREATE TABLE IF NOT EXISTS product_category (
  product_id BIGINT UNSIGNED NOT NULL,
  category_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (product_id, category_id),
  CONSTRAINT fk_product_category_product
    FOREIGN KEY (product_id) REFERENCES product(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_product_category_category
    FOREIGN KEY (category_id) REFERENCES category(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- Books: work + edition
-- =========================================

CREATE TABLE IF NOT EXISTS book_work (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  slug VARCHAR(160) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_book_work_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS book (
  product_id BIGINT UNSIGNED NOT NULL,
  work_id BIGINT UNSIGNED NOT NULL,
  editorial_id BIGINT UNSIGNED NOT NULL,
  lang VARCHAR(5) NOT NULL,
  format VARCHAR(20) NOT NULL,

  isbn VARCHAR(32) NULL,
  pages INT NULL,
  edition VARCHAR(60) NULL,
  synopsis TEXT NULL,
  published_year SMALLINT NULL,

  PRIMARY KEY (product_id),

  UNIQUE KEY uq_book_isbn (isbn),
  UNIQUE KEY uq_book_work_lang_format (work_id, lang, format),

  CONSTRAINT fk_book_product
    FOREIGN KEY (product_id) REFERENCES product(id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_book_work
    FOREIGN KEY (work_id) REFERENCES book_work(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT fk_book_editorial
    FOREIGN KEY (editorial_id) REFERENCES editorial(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT chk_book_lang CHECK (lang IN ('es', 'en')),
  CONSTRAINT chk_book_format CHECK (format IN ('paperback', 'hardcover', 'ebook')),
  CONSTRAINT chk_book_pages_pos CHECK (pages IS NULL OR pages > 0),
  CONSTRAINT chk_book_year CHECK (published_year IS NULL OR (published_year >= 1450 AND published_year <= 2100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS book_work_author (
  work_id BIGINT UNSIGNED NOT NULL,
  author_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (work_id, author_id),
  CONSTRAINT fk_bwa_work
    FOREIGN KEY (work_id) REFERENCES book_work(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_bwa_author
    FOREIGN KEY (author_id) REFERENCES author(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- Packs
-- =========================================

CREATE TABLE IF NOT EXISTS pack (
  product_id BIGINT UNSIGNED NOT NULL,
  description TEXT NULL,
  PRIMARY KEY (product_id),
  CONSTRAINT fk_pack_product
    FOREIGN KEY (product_id) REFERENCES product(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pack_item (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  pack_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uq_pack_item_line (pack_id, product_id),
  CONSTRAINT fk_pack_item_pack
    FOREIGN KEY (pack_id) REFERENCES pack(product_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_pack_item_product
    FOREIGN KEY (product_id) REFERENCES product(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT chk_pack_item_qty CHECK (quantity > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- Auth: user-role N:M
-- =========================================

CREATE TABLE IF NOT EXISTS user_role (
  user_id BIGINT UNSIGNED NOT NULL,
  role_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (user_id, role_id),
  CONSTRAINT fk_user_role_user
    FOREIGN KEY (user_id) REFERENCES `user`(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_user_role_role
    FOREIGN KEY (role_id) REFERENCES role(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- Cart / Wishlist / Rating
-- =========================================

CREATE TABLE IF NOT EXISTS cart (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_cart_user (user_id),
  CONSTRAINT fk_cart_user
    FOREIGN KEY (user_id) REFERENCES `user`(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cart_item (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  cart_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uq_cart_item_line (cart_id, product_id),
  CONSTRAINT fk_cart_item_cart
    FOREIGN KEY (cart_id) REFERENCES cart(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_cart_item_product
    FOREIGN KEY (product_id) REFERENCES product(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT chk_cart_item_qty CHECK (quantity > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS wishlist_item (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_wishlist_line (user_id, product_id),
  CONSTRAINT fk_wishlist_user
    FOREIGN KEY (user_id) REFERENCES `user`(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_wishlist_product
    FOREIGN KEY (product_id) REFERENCES product(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rating (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  value TINYINT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_rating_once (user_id, product_id),
  CONSTRAINT fk_rating_user
    FOREIGN KEY (user_id) REFERENCES `user`(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_rating_product
    FOREIGN KEY (product_id) REFERENCES product(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT chk_rating_value CHECK (value BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- Seed data: categories, editorial, authors, works, products (ES/EN), mapping
-- =========================================

-- Categories (slug = JSON token)
INSERT INTO category (name, slug) VALUES
('Fantasy', 'fantasy'),
('Cosmere', 'cosmere'),
('Grimdark', 'grimdark'),
('Horror', 'horror'),
('Thriller', 'thriller'),
('Historical', 'historical'),
('Science fiction', 'science-fiction'),
('Space opera', 'space-opera'),
('Epic', 'epic')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Single default editorial (JSON doesn't contain editorial)
INSERT INTO editorial (name) VALUES
('Blackrose Press')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Authors (from JSON)
INSERT INTO author (name) VALUES
('T.J.M Seyton'),
('Brandon Sanderson'),
('Joe Abercrombie'),
('Stephen King'),
('Carmen Mola'),
('Matt Dinniman'),
('Christopher Ruocchio'),
('Patrick Rothfuss'),
('George R.R. Martin'),
('George R. R. Martin')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Book works (one per pair ES/EN, id order 1..19)
INSERT INTO book_work (slug) VALUES
('academia-lyndale-saga-linaje'),
('islas-ascuaoscura'),
('trenza-mar-esmeralda'),
('los-diablos'),
('los-heroes'),
('el-instituto'),
('la-bestia'),
('carl-el-mazmorrero'),
('el-imperio-del-silencio'),
('mistborn'),
('name-of-the-wind'),
('wise-mans-fear'),
('narrow-road-between-desires'),
('slow-regard-of-silent-things'),
('game-of-thrones'),
('clash-of-kings'),
('storm-of-swords'),
('feast-for-crows'),
('dance-with-dragons');

-- Map work -> author (one per work, based on your JSON list)
-- (George has two spellings in JSON; we'll normalize to 'George R.R. Martin' for ES works and 'George R. R. Martin' for EN works by mapping both.)
INSERT INTO book_work_author (work_id, author_id)
SELECT 1,  (SELECT id FROM author WHERE name='T.J.M Seyton') UNION ALL
SELECT 2,  (SELECT id FROM author WHERE name='Brandon Sanderson') UNION ALL
SELECT 3,  (SELECT id FROM author WHERE name='Brandon Sanderson') UNION ALL
SELECT 4,  (SELECT id FROM author WHERE name='Joe Abercrombie') UNION ALL
SELECT 5,  (SELECT id FROM author WHERE name='Joe Abercrombie') UNION ALL
SELECT 6,  (SELECT id FROM author WHERE name='Stephen King') UNION ALL
SELECT 7,  (SELECT id FROM author WHERE name='Carmen Mola') UNION ALL
SELECT 8,  (SELECT id FROM author WHERE name='Matt Dinniman') UNION ALL
SELECT 9,  (SELECT id FROM author WHERE name='Christopher Ruocchio') UNION ALL
SELECT 10, (SELECT id FROM author WHERE name='Brandon Sanderson') UNION ALL
SELECT 11, (SELECT id FROM author WHERE name='Patrick Rothfuss') UNION ALL
SELECT 12, (SELECT id FROM author WHERE name='Patrick Rothfuss') UNION ALL
SELECT 13, (SELECT id FROM author WHERE name='Patrick Rothfuss') UNION ALL
SELECT 14, (SELECT id FROM author WHERE name='Patrick Rothfuss') UNION ALL
SELECT 15, (SELECT id FROM author WHERE name='George R.R. Martin') UNION ALL
SELECT 16, (SELECT id FROM author WHERE name='George R.R. Martin') UNION ALL
SELECT 17, (SELECT id FROM author WHERE name='George R.R. Martin') UNION ALL
SELECT 18, (SELECT id FROM author WHERE name='George R.R. Martin') UNION ALL
SELECT 19, (SELECT id FROM author WHERE name='George R.R. Martin')
ON DUPLICATE KEY UPDATE work_id = work_id;

-- -----------------------------------------
-- Insert products + book editions (ES)
-- We'll use stock=50, editorial_id = 'Blackrose Press'
-- -----------------------------------------
INSERT INTO product (name, price, stock, image_path, is_active, is_featured) VALUES
('La Academia Lyndale: Saga Linaje', 18.90, 50, 'assets/images/books/es/academia-lyndale.jpg', 1, 1),
('Islas de la ascuaoscura',          25.87, 50, 'assets/images/books/es/islas-ascuaoscura.jpg', 1, 1),
('Trenza del mar esmeralda',         14.38, 50, 'assets/images/books/es/trenza-mar-esmeralda.jpg', 1, 0),
('Los diablos',                      23.03, 50, 'assets/images/books/es/los-diablos.jpg', 1, 0),
('Los héroes',                       28.37, 50, 'assets/images/books/es/los-heroes.jpg', 1, 1),
('El instituto',                     14.38, 50, 'assets/images/books/es/el-instituto.jpg', 1, 1),
('La bestia',                        12.45, 50, 'assets/images/books/es/la-bestia.jpg', 1, 0),
('Carl el mazmorrero',               22.02, 50, 'assets/images/books/es/carl-mazmorrero.jpg', 1, 1),
('El imperio del silencio',          25.91, 50, 'assets/images/books/es/el-imperio-del-silencio.jpg', 1, 0),
('Nacidos de la bruma',              16.30, 50, 'assets/images/books/es/mistborn-limited.jpg', 1, 1),
('El nombre del viento',             14.95, 50, 'assets/images/books/es/nombre-del-viento.jpg', 1, 1),
('El temor de un hombre sabio',      15.95, 50, 'assets/images/books/es/temor-de-hombre-sabio.jpg', 1, 1),
('El estrecho sendero entre deseos', 21.90, 50, 'assets/images/books/es/estrecho-sendero.png', 1, 0),
('La música del silencio',           18.90, 50, 'assets/images/books/es/musica-del-silencio.jpg', 1, 0),
('Juego de tronos',                  27.90, 50, 'assets/images/books/es/juego-de-tronos.jpg', 1, 1),
('Choque de reyes',                  27.90, 50, 'assets/images/books/es/choque-de-reyes.jpg', 1, 0),
('Tormenta de espadas',              29.90, 50, 'assets/images/books/es/tormenta-de-espadas.jpg', 1, 1),
('Festín de cuervos',                27.90, 50, 'assets/images/books/es/festin-de-cuervos.jpg', 1, 0),
('Danza de dragones',                29.90, 50, 'assets/images/books/es/danza-de-dragones.jpg', 1, 1);

-- Create ES book rows pointing to work_id 1..19, using the last inserted ids (assumes empty DB run)
SET @ed_id := (SELECT id FROM editorial WHERE name='Blackrose Press' LIMIT 1);

-- ES products inserted first => ids 1..19
INSERT INTO book (product_id, work_id, editorial_id, lang, format)
VALUES
(1,  1,  @ed_id, 'es', 'paperback'),
(2,  2,  @ed_id, 'es', 'hardcover'),
(3,  3,  @ed_id, 'es', 'paperback'),
(4,  4,  @ed_id, 'es', 'hardcover'),
(5,  5,  @ed_id, 'es', 'hardcover'),
(6,  6,  @ed_id, 'es', 'paperback'),
(7,  7,  @ed_id, 'es', 'paperback'),
(8,  8,  @ed_id, 'es', 'paperback'),
(9,  9,  @ed_id, 'es', 'paperback'),
(10, 10, @ed_id, 'es', 'paperback'),
(11, 11, @ed_id, 'es', 'hardcover'),
(12, 12, @ed_id, 'es', 'hardcover'),
(13, 13, @ed_id, 'es', 'hardcover'),
(14, 14, @ed_id, 'es', 'hardcover'),
(15, 15, @ed_id, 'es', 'hardcover'),
(16, 16, @ed_id, 'es', 'hardcover'),
(17, 17, @ed_id, 'es', 'hardcover'),
(18, 18, @ed_id, 'es', 'hardcover'),
(19, 19, @ed_id, 'es', 'hardcover');

-- ES categories mapping (product_id 1..19)
-- Helper: get category id by slug
INSERT INTO product_category (product_id, category_id)
SELECT 1, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 2, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 2, id FROM category WHERE slug='cosmere' UNION ALL
SELECT 3, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 3, id FROM category WHERE slug='cosmere' UNION ALL
SELECT 4, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 4, id FROM category WHERE slug='grimdark' UNION ALL
SELECT 5, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 5, id FROM category WHERE slug='grimdark' UNION ALL
SELECT 6, id FROM category WHERE slug='horror' UNION ALL
SELECT 6, id FROM category WHERE slug='thriller' UNION ALL
SELECT 7, id FROM category WHERE slug='thriller' UNION ALL
SELECT 7, id FROM category WHERE slug='historical' UNION ALL
SELECT 8, id FROM category WHERE slug='science-fiction' UNION ALL
SELECT 8, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 9, id FROM category WHERE slug='science-fiction' UNION ALL
SELECT 9, id FROM category WHERE slug='space-opera' UNION ALL
SELECT 10, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 10, id FROM category WHERE slug='cosmere' UNION ALL
SELECT 11, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 12, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 13, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 14, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 15, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 15, id FROM category WHERE slug='epic' UNION ALL
SELECT 16, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 16, id FROM category WHERE slug='epic' UNION ALL
SELECT 17, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 17, id FROM category WHERE slug='epic' UNION ALL
SELECT 18, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 18, id FROM category WHERE slug='epic' UNION ALL
SELECT 19, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 19, id FROM category WHERE slug='epic'
ON DUPLICATE KEY UPDATE product_id = product_id;

-- -----------------------------------------
-- Insert products + book editions (EN)
-- We'll continue IDs 20..38 (same empty DB assumption)
-- -----------------------------------------
INSERT INTO product (name, price, stock, image_path, is_active, is_featured) VALUES
('La Academia Lyndale: Saga Linaje', 18.90, 50, 'assets/images/books/es/academia-lyndale.jpg', 1, 1),
('Isles of the Emberdark',           25.87, 50, 'assets/images/books/en/islas-ascuaoscura-en.jpg', 1, 1),
('Tress of the emerald sea',         14.38, 50, 'assets/images/books/en/trenza-mar-esmeralda-en.jpg', 1, 0),
('The Devils',                       23.03, 50, 'assets/images/books/en/los-diablos-en.jpg', 1, 0),
('The Heroes',                       28.37, 50, 'assets/images/books/en/los-heroes-en.jpg', 1, 1),
('The Institute',                    14.38, 50, 'assets/images/books/en/el-instituto-en.jpg', 1, 1),
('La Bestia',                        12.45, 50, 'assets/images/books/en/la-bestia.jpg', 1, 0),
('Dungeon Crawler Carl',             22.02, 50, 'assets/images/books/en/carl-mazmorrero-en.jpg', 1, 1),
('Empire of Silence',                25.91, 50, 'assets/images/books/en/el-imperio-del-silencio-en.jpg', 1, 0),
('Mistborn',                         16.30, 50, 'assets/images/books/en/mistborn-limited-en.jpg', 1, 1),
('The Name of the Wind',             20.70, 50, 'assets/images/books/en/name-of-wind.jpg', 1, 1),
('The Wise Man''s Fear',             22.27, 50, 'assets/images/books/en/wise-mans-fear.jpg', 1, 1),
('The Narrow Road Between Desires',  12.15, 50, 'assets/images/books/en/narrow-road.jpg', 1, 0),
('The Slow Regard of Silent Things', 12.10, 50, 'assets/images/books/en/the-slow-regard.jpg', 1, 0),
('A Game of Thrones',                33.08, 50, 'assets/images/books/en/game-of-thrones.jpg', 1, 1),
('A Clash of Kings',                 37.99, 50, 'assets/images/books/en/clash-of-kings.jpg', 1, 0),
('A Storm of Swords',                 8.60, 50, 'assets/images/books/en/storm-of-swords.jpg', 1, 1),
('A Feast for Crows',                48.65, 50, 'assets/images/books/en/feast-for-crows.jpg', 1, 0),
('A Dance with Dragons',             23.90, 50, 'assets/images/books/en/dance-with-dragons.jpg', 1, 1);

-- EN products are ids 20..38
INSERT INTO book (product_id, work_id, editorial_id, lang, format)
VALUES
(20, 1,  @ed_id, 'en', 'paperback'),
(21, 2,  @ed_id, 'en', 'hardcover'),
(22, 3,  @ed_id, 'en', 'paperback'),
(23, 4,  @ed_id, 'en', 'hardcover'),
(24, 5,  @ed_id, 'en', 'hardcover'),
(25, 6,  @ed_id, 'en', 'paperback'),
(26, 7,  @ed_id, 'en', 'paperback'),
(27, 8,  @ed_id, 'en', 'paperback'),
(28, 9,  @ed_id, 'en', 'paperback'),
(29, 10, @ed_id, 'en', 'paperback'),
(30, 11, @ed_id, 'en', 'hardcover'),
(31, 12, @ed_id, 'en', 'hardcover'),
(32, 13, @ed_id, 'en', 'hardcover'),
(33, 14, @ed_id, 'en', 'paperback'),
(34, 15, @ed_id, 'en', 'hardcover'),
(35, 16, @ed_id, 'en', 'hardcover'),
(36, 17, @ed_id, 'en', 'paperback'),
(37, 18, @ed_id, 'en', 'hardcover'),
(38, 19, @ed_id, 'en', 'hardcover');

-- EN categories mapping (product_id 20..38) (same as ES in your JSON)
INSERT INTO product_category (product_id, category_id)
SELECT 20, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 21, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 21, id FROM category WHERE slug='cosmere' UNION ALL
SELECT 22, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 22, id FROM category WHERE slug='cosmere' UNION ALL
SELECT 23, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 23, id FROM category WHERE slug='grimdark' UNION ALL
SELECT 24, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 24, id FROM category WHERE slug='grimdark' UNION ALL
SELECT 25, id FROM category WHERE slug='horror' UNION ALL
SELECT 25, id FROM category WHERE slug='thriller' UNION ALL
SELECT 26, id FROM category WHERE slug='thriller' UNION ALL
SELECT 26, id FROM category WHERE slug='historical' UNION ALL
SELECT 27, id FROM category WHERE slug='science-fiction' UNION ALL
SELECT 27, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 28, id FROM category WHERE slug='science-fiction' UNION ALL
SELECT 28, id FROM category WHERE slug='space-opera' UNION ALL
SELECT 29, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 29, id FROM category WHERE slug='cosmere' UNION ALL
SELECT 30, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 31, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 32, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 33, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 34, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 34, id FROM category WHERE slug='epic' UNION ALL
SELECT 35, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 35, id FROM category WHERE slug='epic' UNION ALL
SELECT 36, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 36, id FROM category WHERE slug='epic' UNION ALL
SELECT 37, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 37, id FROM category WHERE slug='epic' UNION ALL
SELECT 38, id FROM category WHERE slug='fantasy' UNION ALL
SELECT 38, id FROM category WHERE slug='epic'
ON DUPLICATE KEY UPDATE product_id = product_id;

-- =========================================
-- Minimal roles seed
-- =========================================
INSERT INTO role (name) VALUES ('user'), ('admin')
    ON DUPLICATE KEY UPDATE name = VALUES(name);