
-- ======================
-- |  Bookstore schema  |
-- ======================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

DROP DATABASE IF EXISTS bookstore;

CREATE DATABASE IF NOT EXISTS bookstore
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE bookstore;


-- ==============================
-- |  Drop tables (safe reset)  |
-- ==============================

DROP TABLE IF EXISTS pack_items;
DROP TABLE IF EXISTS packs;
DROP TABLE IF EXISTS ratings;
DROP TABLE IF EXISTS wishlist_items;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS carts;
DROP TABLE IF EXISTS user_roles;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS book_work_authors;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS book_works;
DROP TABLE IF EXISTS product_categories;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS authors;
DROP TABLE IF EXISTS editorials;
DROP TABLE IF EXISTS categories;



-- =================
-- |  Base tables  |
-- =================

CREATE TABLE IF NOT EXISTS categories (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_category_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS editorials (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,

  PRIMARY KEY (id),
  UNIQUE KEY uq_editorial_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS authors (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,

  PRIMARY KEY (id),
  UNIQUE KEY uq_author_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS roles (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,

  PRIMARY KEY (id),
  UNIQUE KEY uq_role_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO roles (name) VALUES
  ('user'),
  ('admin')
ON DUPLICATE KEY UPDATE name = VALUES(name);


CREATE TABLE IF NOT EXISTS users (
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



-- ===========================
-- |  Product catalog tables |
-- ===========================

CREATE TABLE IF NOT EXISTS products (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  image_path VARCHAR(255) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_products_slug (slug),

  CONSTRAINT chk_product_price_nonneg CHECK (price >= 0),
  CONSTRAINT chk_product_stock_nonneg CHECK (stock >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS product_categories (
  product_id BIGINT UNSIGNED NOT NULL,
  category_id BIGINT UNSIGNED NOT NULL,

  PRIMARY KEY (product_id, category_id),
  KEY idx_product_categories_category_id (category_id),

  CONSTRAINT fk_product_category_product
    FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_product_category_category
    FOREIGN KEY (category_id) REFERENCES categories(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- =============================
-- |  Books: work + edition    |
-- =============================

CREATE TABLE IF NOT EXISTS book_works (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  slug VARCHAR(160) NOT NULL,

  PRIMARY KEY (id),
  UNIQUE KEY uq_book_work_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS books (
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

  KEY idx_books_work_id (work_id),
  KEY idx_books_editorial_id (editorial_id),
  KEY idx_books_lang (lang),

  CONSTRAINT fk_book_product
    FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_book_work
    FOREIGN KEY (work_id) REFERENCES book_works(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT fk_book_editorial
    FOREIGN KEY (editorial_id) REFERENCES editorials(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT chk_book_lang CHECK (lang IN ('es', 'en')),
  CONSTRAINT chk_book_format CHECK (format IN ('paperback', 'hardcover', 'ebook')),
  CONSTRAINT chk_book_pages_pos CHECK (pages IS NULL OR pages > 0),
  CONSTRAINT chk_book_year CHECK (published_year IS NULL OR (published_year >= 1450 AND published_year <= 2100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS book_work_authors (
  work_id BIGINT UNSIGNED NOT NULL,
  author_id BIGINT UNSIGNED NOT NULL,

  PRIMARY KEY (work_id, author_id),
  KEY idx_book_work_authors_author_id (author_id),

  CONSTRAINT fk_bwa_work
    FOREIGN KEY (work_id) REFERENCES book_works(id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_bwa_author
    FOREIGN KEY (author_id) REFERENCES authors(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- ============
-- |  Packs   |
-- ============

CREATE TABLE IF NOT EXISTS packs (
  product_id BIGINT UNSIGNED NOT NULL,
  description TEXT NULL,

  PRIMARY KEY (product_id),

  CONSTRAINT fk_pack_product
    FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS pack_items (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  pack_product_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  quantity INT NOT NULL DEFAULT 1,

  PRIMARY KEY (id),
  UNIQUE KEY uq_pack_item_line (pack_product_id, product_id),

  KEY idx_pack_items_pack_product_id (pack_product_id),
  KEY idx_pack_items_product_id (product_id),

  CONSTRAINT fk_pack_item_pack
    FOREIGN KEY (pack_product_id) REFERENCES packs(product_id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_pack_item_product
    FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT chk_pack_item_qty CHECK (quantity > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- ========================
-- |  Auth: user-role N:M |
-- ========================

CREATE TABLE IF NOT EXISTS user_roles (
  user_id BIGINT UNSIGNED NOT NULL,
  role_id BIGINT UNSIGNED NOT NULL,

  PRIMARY KEY (user_id, role_id),
  KEY idx_user_roles_role_id (role_id),

  CONSTRAINT fk_user_role_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_user_role_role
    FOREIGN KEY (role_id) REFERENCES roles(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- ==============================
-- |  Cart / Wishlist / Rating  |
-- ==============================

CREATE TABLE IF NOT EXISTS carts (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_cart_user (user_id),

  CONSTRAINT fk_cart_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS cart_items (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  cart_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  quantity INT NOT NULL DEFAULT 1,

  PRIMARY KEY (id),
  UNIQUE KEY uq_cart_item_line (cart_id, product_id),

  KEY idx_cart_items_cart_id (cart_id),
  KEY idx_cart_items_product_id (product_id),

  CONSTRAINT fk_cart_item_cart
    FOREIGN KEY (cart_id) REFERENCES carts(id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_cart_item_product
    FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT chk_cart_item_qty CHECK (quantity > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS wishlist_items (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_wishlist_line (user_id, product_id),

  KEY idx_wishlist_items_user_id (user_id),
  KEY idx_wishlist_items_product_id (product_id),

  CONSTRAINT fk_wishlist_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_wishlist_product
    FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS ratings (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  value TINYINT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_rating_once (user_id, product_id),

  KEY idx_ratings_user_id (user_id),
  KEY idx_ratings_product_id (product_id),

  CONSTRAINT fk_rating_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT fk_rating_product
    FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,

  CONSTRAINT chk_rating_value CHECK (value BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- =========================
-- |  Seed (coherente)     |
-- =========================

-- Editorials
INSERT INTO editorials (name) VALUES
  ('Ace'),
  ('Alianza Editorial'),
  ('Bantam Books'),
  ('DAW Books'),
  ('Editorial Blackrose'),
  ('Editorial Planeta'),
  ('Gollancz'),
  ('Nova'),
  ('Oz Editorial'),
  ('Plaza & Janés'),
  ('Tor Books')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Authors
INSERT INTO authors (name) VALUES
  ('T.J.M Seyton'),
  ('Brandon Sanderson'),
  ('Joe Abercrombie'),
  ('Stephen King'),
  ('Patrick Rothfuss'),
  ('Christopher Ruocchio'),
  ('Carmen Mola'),
  ('Matt Dinniman'),
  ('George R. R. Martin')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Categories (incluye las usadas en tu mapping)
INSERT INTO categories (name, slug) VALUES
  ('Fantasy', 'fantasy'),
  ('Epic', 'epic'),
  ('Science fiction', 'science-fiction'),
  ('Space opera', 'space-opera'),
  ('Thriller', 'thriller'),
  ('Horror', 'horror'),
  ('Cosmere', 'cosmere'),
  ('Grimdark', 'grimdark'),
  ('Historical', 'historical')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Book works (slugs estables)
INSERT INTO book_works (slug) VALUES
  ('mistborn-1-el-imperio-final'),
  ('mistborn-2-el-pozo-de-la-ascension'),
  ('mistborn-3-el-heroe-de-las-eras'),
  ('la-primera-ley-1-la-voz-de-las-espadas'),
  ('la-primera-ley-2-antes-de-que-los-cuelguen'),
  ('la-primera-ley-3-el-ultimo-argumento-de-los-reyes'),
  ('el-instituto'),
  ('el-nombre-del-viento'),
  ('el-imperio-del-silencio'),
  ('academia-lyndale-saga-linaje'),
  ('islas-ascuaoscura'),
  ('trenza-mar-esmeralda'),
  ('the-devils'),
  ('the-heroes'),
  ('la-bestia'),
  ('dungeon-crawler-carl'),
  ('wise-mans-fear'),
  ('slow-regard-of-silent-things'),
  ('narrow-road-between-desires'),
  ('game-of-thrones'),
  ('clash-of-kings'),
  ('storm-of-swords'),
  ('feast-for-crows'),
  ('dance-with-dragons')
ON DUPLICATE KEY UPDATE slug = VALUES(slug);

-- Work <-> Author (1 autor por obra)
INSERT INTO book_work_authors (work_id, author_id)
SELECT w.id, a.id FROM book_works w JOIN authors a ON a.name='Brandon Sanderson'
WHERE w.slug IN ('mistborn-1-el-imperio-final','mistborn-2-el-pozo-de-la-ascension','mistborn-3-el-heroe-de-las-eras','islas-ascuaoscura','trenza-mar-esmeralda')
ON DUPLICATE KEY UPDATE work_id = work_id;

INSERT INTO book_work_authors (work_id, author_id)
SELECT w.id, a.id FROM book_works w JOIN authors a ON a.name='Joe Abercrombie'
WHERE w.slug IN ('la-primera-ley-1-la-voz-de-las-espadas','la-primera-ley-2-antes-de-que-los-cuelguen','la-primera-ley-3-el-ultimo-argumento-de-los-reyes','the-devils','the-heroes')
ON DUPLICATE KEY UPDATE work_id = work_id;

INSERT INTO book_work_authors (work_id, author_id)
SELECT w.id, a.id FROM book_works w JOIN authors a ON a.name='Stephen King'
WHERE w.slug = 'el-instituto'
ON DUPLICATE KEY UPDATE work_id = work_id;

INSERT INTO book_work_authors (work_id, author_id)
SELECT w.id, a.id FROM book_works w JOIN authors a ON a.name='Patrick Rothfuss'
WHERE w.slug IN ('el-nombre-del-viento','wise-mans-fear','slow-regard-of-silent-things','narrow-road-between-desires')
ON DUPLICATE KEY UPDATE work_id = work_id;

INSERT INTO book_work_authors (work_id, author_id)
SELECT w.id, a.id FROM book_works w JOIN authors a ON a.name='Christopher Ruocchio'
WHERE w.slug = 'el-imperio-del-silencio'
ON DUPLICATE KEY UPDATE work_id = work_id;

INSERT INTO book_work_authors (work_id, author_id)
SELECT w.id, a.id FROM book_works w JOIN authors a ON a.name='T.J.M Seyton'
WHERE w.slug = 'academia-lyndale-saga-linaje'
ON DUPLICATE KEY UPDATE work_id = work_id;

INSERT INTO book_work_authors (work_id, author_id)
SELECT w.id, a.id FROM book_works w JOIN authors a ON a.name='Carmen Mola'
WHERE w.slug = 'la-bestia'
ON DUPLICATE KEY UPDATE work_id = work_id;

INSERT INTO book_work_authors (work_id, author_id)
SELECT w.id, a.id FROM book_works w JOIN authors a ON a.name='Matt Dinniman'
WHERE w.slug = 'dungeon-crawler-carl'
ON DUPLICATE KEY UPDATE work_id = work_id;

INSERT INTO book_work_authors (work_id, author_id)
SELECT w.id, a.id FROM book_works w JOIN authors a ON a.name='George R. R. Martin'
WHERE w.slug IN ('game-of-thrones','clash-of-kings','storm-of-swords','feast-for-crows','dance-with-dragons')
ON DUPLICATE KEY UPDATE work_id = work_id;

-- Helpers de IDs
SET @ed_nova := (SELECT id FROM editorials WHERE name='Nova' LIMIT 1);
SET @ed_alianza := (SELECT id FROM editorials WHERE name='Alianza Editorial' LIMIT 1);
SET @ed_plaza := (SELECT id FROM editorials WHERE name='Plaza & Janés' LIMIT 1);
SET @ed_oz := (SELECT id FROM editorials WHERE name='Oz Editorial' LIMIT 1);
SET @ed_blackrose := (SELECT id FROM editorials WHERE name='Editorial Blackrose' LIMIT 1);
SET @ed_planeta := (SELECT id FROM editorials WHERE name='Editorial Planeta' LIMIT 1);
SET @ed_tor := (SELECT id FROM editorials WHERE name='Tor Books' LIMIT 1);
SET @ed_gollancz := (SELECT id FROM editorials WHERE name='Gollancz' LIMIT 1);
SET @ed_daw := (SELECT id FROM editorials WHERE name='DAW Books' LIMIT 1);
SET @ed_ace := (SELECT id FROM editorials WHERE name='Ace' LIMIT 1);
SET @ed_bantam := (SELECT id FROM editorials WHERE name='Bantam Books' LIMIT 1);

SET @cat_fantasy := (SELECT id FROM categories WHERE slug='fantasy' LIMIT 1);
SET @cat_epic := (SELECT id FROM categories WHERE slug='epic' LIMIT 1);
SET @cat_scifi := (SELECT id FROM categories WHERE slug='science-fiction' LIMIT 1);
SET @cat_space := (SELECT id FROM categories WHERE slug='space-opera' LIMIT 1);
SET @cat_thriller := (SELECT id FROM categories WHERE slug='thriller' LIMIT 1);
SET @cat_horror := (SELECT id FROM categories WHERE slug='horror' LIMIT 1);
SET @cat_cosmere := (SELECT id FROM categories WHERE slug='cosmere' LIMIT 1);
SET @cat_grimdark := (SELECT id FROM categories WHERE slug='grimdark' LIMIT 1);
SET @cat_historical := (SELECT id FROM categories WHERE slug='historical' LIMIT 1);

-- =========================
-- | Products + Books (ES) |
-- =========================

-- Mistborn 1
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('El imperio final (Nacidos de la Bruma 1)', 'el-imperio-final-nacidos-de-la-bruma-1', 24.90, 50, 'assets/images/books/es/mistborn-1.jpg', 1, 1);
SET @p_m1 := (SELECT id FROM products WHERE slug='el-imperio-final-nacidos-de-la-bruma-1' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_m1,(SELECT id FROM book_works WHERE slug='mistborn-1-el-imperio-final'),@ed_nova,'es','hardcover','9788417347291',688,NULL,NULL,NULL);

-- Mistborn 2
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('El pozo de la ascensión (Nacidos de la Bruma 2)', 'el-pozo-de-la-ascension-nacidos-de-la-bruma-2', 24.90, 50, 'assets/images/books/es/mistborn-2.jpg', 1, 1);
SET @p_m2 := (SELECT id FROM products WHERE slug='el-pozo-de-la-ascension-nacidos-de-la-bruma-2' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_m2,(SELECT id FROM book_works WHERE slug='mistborn-2-el-pozo-de-la-ascension'),@ed_nova,'es','hardcover','9788466658904',800,NULL,NULL,NULL);

-- Mistborn 3
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('El héroe de las eras (Nacidos de la Bruma 3)', 'el-heroe-de-las-eras-nacidos-de-la-bruma-3', 24.90, 50, 'assets/images/books/es/mistborn-3.jpg', 1, 1);
SET @p_m3 := (SELECT id FROM products WHERE slug='el-heroe-de-las-eras-nacidos-de-la-bruma-3' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_m3,(SELECT id FROM book_works WHERE slug='mistborn-3-el-heroe-de-las-eras'),@ed_nova,'es','hardcover','9788418037290',760,NULL,NULL,NULL);

-- La Primera Ley 1
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('La voz de las espadas (La Primera Ley 1)', 'la-voz-de-las-espadas-la-primera-ley-1', 19.95, 50, 'assets/images/books/es/primera-ley-1.jpg', 1, 1);
SET @p_fl1 := (SELECT id FROM products WHERE slug='la-voz-de-las-espadas-la-primera-ley-1' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_fl1,(SELECT id FROM book_works WHERE slug='la-primera-ley-1-la-voz-de-las-espadas'),@ed_alianza,'es','hardcover','9788411486316',736,NULL,NULL,NULL);

-- La Primera Ley 2
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('Antes de que los cuelguen (La Primera Ley 2)', 'antes-de-que-los-cuelguen-la-primera-ley-2', 19.95, 50, 'assets/images/books/es/primera-ley-2.jpg', 1, 0);
SET @p_fl2 := (SELECT id FROM products WHERE slug='antes-de-que-los-cuelguen-la-primera-ley-2' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_fl2,(SELECT id FROM book_works WHERE slug='la-primera-ley-2-antes-de-que-los-cuelguen'),@ed_alianza,'es','paperback','9788411480734',700,NULL,NULL,NULL);

-- La Primera Ley 3
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('El último argumento de los reyes (La Primera Ley 3)', 'el-ultimo-argumento-de-los-reyes-la-primera-ley-3', 19.95, 50, 'assets/images/books/es/primera-ley-3.jpg', 1, 0);
SET @p_fl3 := (SELECT id FROM products WHERE slug='el-ultimo-argumento-de-los-reyes-la-primera-ley-3' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_fl3,(SELECT id FROM book_works WHERE slug='la-primera-ley-3-el-ultimo-argumento-de-los-reyes'),@ed_alianza,'es','hardcover','9788411486330',880,NULL,NULL,NULL);

-- El Instituto (ES)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('El Instituto', 'el-instituto', 24.90, 50, 'assets/images/books/es/el-instituto.jpg', 1, 1);
SET @p_inst_es := (SELECT id FROM products WHERE slug='el-instituto' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_inst_es,(SELECT id FROM book_works WHERE slug='el-instituto'),@ed_plaza,'es','hardcover','9788401022357',624,NULL,NULL,NULL);

-- El nombre del viento (ES)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('El nombre del viento', 'el-nombre-del-viento', 22.90, 50, 'assets/images/books/es/el-nombre-del-viento.jpg', 1, 1);
SET @p_notw_es := (SELECT id FROM products WHERE slug='el-nombre-del-viento' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_notw_es,(SELECT id FROM book_works WHERE slug='el-nombre-del-viento'),@ed_plaza,'es','paperback','9788401337208',880,NULL,NULL,NULL);

-- El imperio del silencio (ES)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('El imperio del silencio', 'el-imperio-del-silencio', 26.95, 50, 'assets/images/books/es/el-imperio-del-silencio.jpg', 1, 0);
SET @p_eos_es := (SELECT id FROM products WHERE slug='el-imperio-del-silencio' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_eos_es,(SELECT id FROM book_works WHERE slug='el-imperio-del-silencio'),@ed_oz,'es','paperback','9788418431111',768,NULL,NULL,NULL);

-- La Academia Lyndale (ES)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('La Academia Lyndale: Saga Linaje', 'la-academia-lyndale-saga-linaje', 18.90, 50, 'assets/images/books/es/academia-lyndale.jpg', 1, 1);
SET @p_lyndale_es := (SELECT id FROM products WHERE slug='la-academia-lyndale-saga-linaje' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_lyndale_es,(SELECT id FROM book_works WHERE slug='academia-lyndale-saga-linaje'),@ed_blackrose,'es','paperback','9788409716166',480,NULL,NULL,2025);

-- Islas de la ascuaoscura (ES)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('Islas de la ascuaoscura', 'islas-de-la-ascuaoscura', 25.87, 50, 'assets/images/books/es/islas-ascuaoscura.jpg', 1, 1);
SET @p_islas_es := (SELECT id FROM products WHERE slug='islas-de-la-ascuaoscura' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_islas_es,(SELECT id FROM book_works WHERE slug='islas-ascuaoscura'),@ed_nova,'es','hardcover','9788419260574',560,NULL,NULL,2025);

-- Trenza del mar esmeralda (ES)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('Trenza del mar esmeralda', 'trenza-del-mar-esmeralda', 14.38, 50, 'assets/images/books/es/trenza-mar-esmeralda.jpg', 1, 0);
SET @p_trenza_es := (SELECT id FROM products WHERE slug='trenza-del-mar-esmeralda' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_trenza_es,(SELECT id FROM book_works WHERE slug='trenza-mar-esmeralda'),@ed_nova,'es','hardcover','9788418037818',560,NULL,NULL,2023);

-- Los héroes (ES)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('Los héroes', 'los-heroes', 28.37, 50, 'assets/images/books/es/los-heroes.jpg', 1, 1);
SET @p_heroes_es := (SELECT id FROM products WHERE slug='los-heroes' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_heroes_es,(SELECT id FROM book_works WHERE slug='the-heroes'),@ed_alianza,'es','hardcover','9788411488310',768,NULL,NULL,2024);

-- La bestia (ES)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('La bestia', 'la-bestia', 12.45, 50, 'assets/images/books/es/la-bestia.jpg', 1, 0);
SET @p_bestia_es := (SELECT id FROM products WHERE slug='la-bestia' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_bestia_es,(SELECT id FROM book_works WHERE slug='la-bestia'),@ed_planeta,'es','hardcover','9788408249849',544,NULL,NULL,2021);

-- Carl el mazmorrero (ES)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('Carl el mazmorrero', 'carl-el-mazmorrero', 22.02, 50, 'assets/images/books/es/carl-mazmorrero.jpg', 1, 1);
SET @p_carl_es := (SELECT id FROM products WHERE slug='carl-el-mazmorrero' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_carl_es,(SELECT id FROM book_works WHERE slug='dungeon-crawler-carl'),@ed_nova,'es','paperback','9788410466135',464,NULL,NULL,2025);

-- El temor de un hombre sabio (ES)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('El temor de un hombre sabio', 'el-temor-de-un-hombre-sabio', 15.95, 50, 'assets/images/books/es/temor-de-hombre-sabio.jpg', 1, 1);
SET @p_wmf_es := (SELECT id FROM products WHERE slug='el-temor-de-un-hombre-sabio' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_wmf_es,(SELECT id FROM book_works WHERE slug='wise-mans-fear'),@ed_plaza,'es','hardcover','9788401352331',1200,NULL,NULL,2011);

-- La música del silencio (ES)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('La música del silencio', 'la-musica-del-silencio', 18.90, 50, 'assets/images/books/es/musica-del-silencio.jpg', 1, 0);
SET @p_slow_es := (SELECT id FROM products WHERE slug='la-musica-del-silencio' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_slow_es,(SELECT id FROM book_works WHERE slug='slow-regard-of-silent-things'),@ed_plaza,'es','hardcover','9788401343575',152,NULL,NULL,2014);

-- El estrecho sendero entre deseos (ES)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('El estrecho sendero entre deseos', 'el-estrecho-sendero-entre-deseos', 21.90, 50, 'assets/images/books/es/estrecho-sendero.png', 1, 0);
SET @p_narrow_es := (SELECT id FROM products WHERE slug='el-estrecho-sendero-entre-deseos' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_narrow_es,(SELECT id FROM book_works WHERE slug='narrow-road-between-desires'),@ed_plaza,'es','hardcover','9788401032974',240,NULL,NULL,2024);

-- ASOIAF (ES)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('Juego de tronos','juego-de-tronos',27.90,50,'assets/images/books/es/juego-de-tronos.jpg',1,1),
('Choque de reyes','choque-de-reyes',27.90,50,'assets/images/books/es/choque-de-reyes.jpg',1,0),
('Tormenta de espadas','tormenta-de-espadas',29.90,50,'assets/images/books/es/tormenta-de-espadas.jpg',1,1),
('Festín de cuervos','festin-de-cuervos',27.90,50,'assets/images/books/es/festin-de-cuervos.jpg',1,0),
('Danza de dragones','danza-de-dragones',29.90,50,'assets/images/books/es/danza-de-dragones.jpg',1,1);

SET @p_got_es := (SELECT id FROM products WHERE slug='juego-de-tronos' LIMIT 1);
SET @p_clash_es := (SELECT id FROM products WHERE slug='choque-de-reyes' LIMIT 1);
SET @p_storm_es := (SELECT id FROM products WHERE slug='tormenta-de-espadas' LIMIT 1);
SET @p_feast_es := (SELECT id FROM products WHERE slug='festin-de-cuervos' LIMIT 1);
SET @p_dance_es := (SELECT id FROM products WHERE slug='danza-de-dragones' LIMIT 1);

INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year) VALUES
(@p_got_es,(SELECT id FROM book_works WHERE slug='game-of-thrones'),@ed_plaza,'es','hardcover','9788401032424',800,NULL,NULL,2023),
(@p_clash_es,(SELECT id FROM book_works WHERE slug='clash-of-kings'),@ed_plaza,'es','hardcover','9788401032431',928,NULL,NULL,2023),
(@p_storm_es,(SELECT id FROM book_works WHERE slug='storm-of-swords'),@ed_plaza,'es','hardcover','9788401032448',1248,NULL,NULL,2023),
(@p_feast_es,(SELECT id FROM book_works WHERE slug='feast-for-crows'),@ed_plaza,'es','hardcover','9788401032455',872,NULL,NULL,2023),
(@p_dance_es,(SELECT id FROM book_works WHERE slug='dance-with-dragons'),@ed_plaza,'es','hardcover','9788401032462',1136,NULL,NULL,2023);

-- =========================
-- | Products + Books (EN) |
-- =========================

-- La Academia Lyndale (EN) (manteniendo título tal como lo usas)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('La Academia Lyndale: Saga Linaje', 'la-academia-lyndale-saga-linaje-en', 18.90, 50, 'assets/images/books/en/academia-lyndale-en.jpg', 1, 1);
SET @p_lyndale_en := (SELECT id FROM products WHERE slug='la-academia-lyndale-saga-linaje-en' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_lyndale_en,(SELECT id FROM book_works WHERE slug='academia-lyndale-saga-linaje'),@ed_blackrose,'en','paperback',NULL,480,NULL,NULL,2025);

-- Isles of the Emberdark (EN)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('Isles of the Emberdark', 'isles-of-the-emberdark', 25.87, 50, 'assets/images/books/en/islas-ascuaoscura-en.jpg', 1, 1);
SET @p_isles_en := (SELECT id FROM products WHERE slug='isles-of-the-emberdark' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_isles_en,(SELECT id FROM book_works WHERE slug='islas-ascuaoscura'),@ed_tor,'en','hardcover',NULL,560,NULL,NULL,2025);

-- Tress of the Emerald Sea (EN)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('Tress of the Emerald Sea', 'tress-of-the-emerald-sea', 14.38, 50, 'assets/images/books/en/trenza-mar-esmeralda-en.jpg', 1, 0);
SET @p_trenza_en := (SELECT id FROM products WHERE slug='tress-of-the-emerald-sea' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_trenza_en,(SELECT id FROM book_works WHERE slug='trenza-mar-esmeralda'),@ed_tor,'en','hardcover','9781250899668',384,NULL,NULL,2024);

-- The Devils (EN)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('The Devils', 'the-devils', 23.03, 50, 'assets/images/books/en/los-diablos-en.jpg', 1, 0);
SET @p_devils_en := (SELECT id FROM products WHERE slug='the-devils' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_devils_en,(SELECT id FROM book_works WHERE slug='the-devils'),@ed_gollancz,'en','hardcover',NULL,NULL,NULL,NULL,NULL);

-- The Heroes (EN) (pages NULL para cumplir CHECK)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('The Heroes', 'the-heroes', 28.37, 50, 'assets/images/books/en/los-heroes-en.jpg', 1, 1);
SET @p_heroes_en := (SELECT id FROM products WHERE slug='the-heroes' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_heroes_en,(SELECT id FROM book_works WHERE slug='the-heroes'),@ed_gollancz,'en','hardcover','9780575083844',NULL,NULL,NULL,2011);

-- The Institute (EN)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('The Institute', 'the-institute', 14.38, 50, 'assets/images/books/en/el-instituto-en.jpg', 1, 1);
SET @p_inst_en := (SELECT id FROM products WHERE slug='the-institute' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_inst_en,(SELECT id FROM book_works WHERE slug='el-instituto'),@ed_bantam,'en','paperback',NULL,624,NULL,NULL,2019);

-- La Bestia (EN) (manteniendo título)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('La Bestia', 'la-bestia-en', 12.45, 50, 'assets/images/books/en/la-bestia.jpg', 1, 0);
SET @p_bestia_en := (SELECT id FROM products WHERE slug='la-bestia-en' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_bestia_en,(SELECT id FROM book_works WHERE slug='la-bestia'),@ed_planeta,'en','paperback',NULL,544,NULL,NULL,2021);

-- Dungeon Crawler Carl (EN)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('Dungeon Crawler Carl', 'dungeon-crawler-carl', 22.02, 50, 'assets/images/books/en/carl-mazmorrero-en.jpg', 1, 1);
SET @p_carl_en := (SELECT id FROM products WHERE slug='dungeon-crawler-carl' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_carl_en,(SELECT id FROM book_works WHERE slug='dungeon-crawler-carl'),@ed_ace,'en','hardcover','9780593820247',464,NULL,NULL,2024);

-- Empire of Silence (EN)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('Empire of Silence', 'empire-of-silence', 25.91, 50, 'assets/images/books/en/el-imperio-del-silencio-en.jpg', 1, 0);
SET @p_eos_en := (SELECT id FROM products WHERE slug='empire-of-silence' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_eos_en,(SELECT id FROM book_works WHERE slug='el-imperio-del-silencio'),@ed_daw,'en','paperback',NULL,NULL,NULL,NULL,2018);

-- Mistborn (EN) (título simple)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('Mistborn', 'mistborn', 16.30, 50, 'assets/images/books/en/mistborn-limited-en.jpg', 1, 1);
SET @p_mistborn_en := (SELECT id FROM products WHERE slug='mistborn' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_mistborn_en,(SELECT id FROM book_works WHERE slug='mistborn-1-el-imperio-final'),@ed_tor,'en','paperback',NULL,NULL,NULL,NULL,2006);

-- The Name of the Wind (EN)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('The Name of the Wind', 'the-name-of-the-wind', 20.70, 50, 'assets/images/books/en/name-of-wind.jpg', 1, 1);
SET @p_notw_en := (SELECT id FROM products WHERE slug='the-name-of-the-wind' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_notw_en,(SELECT id FROM book_works WHERE slug='el-nombre-del-viento'),@ed_daw,'en','hardcover',NULL,NULL,NULL,NULL,2007);

-- The Wise Man's Fear (EN)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('The Wise Man''s Fear', 'the-wise-mans-fear', 22.27, 50, 'assets/images/books/en/wise-mans-fear.jpg', 1, 1);
SET @p_wmf_en := (SELECT id FROM products WHERE slug='the-wise-mans-fear' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_wmf_en,(SELECT id FROM book_works WHERE slug='wise-mans-fear'),@ed_daw,'en','hardcover','9780756404734',993,NULL,NULL,2011);

-- The Narrow Road Between Desires (EN)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('The Narrow Road Between Desires', 'the-narrow-road-between-desires', 12.15, 50, 'assets/images/books/en/narrow-road.jpg', 1, 0);
SET @p_narrow_en := (SELECT id FROM products WHERE slug='the-narrow-road-between-desires' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_narrow_en,(SELECT id FROM book_works WHERE slug='narrow-road-between-desires'),@ed_daw,'en','hardcover','9780756419172',240,NULL,NULL,2023);

-- The Slow Regard of Silent Things (EN)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('The Slow Regard of Silent Things', 'the-slow-regard-of-silent-things', 12.10, 50, 'assets/images/books/en/the-slow-regard.jpg', 1, 0);
SET @p_slow_en := (SELECT id FROM products WHERE slug='the-slow-regard-of-silent-things' LIMIT 1);
INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year)
VALUES (@p_slow_en,(SELECT id FROM book_works WHERE slug='slow-regard-of-silent-things'),@ed_daw,'en','hardcover','9780756410438',176,NULL,NULL,2014);

-- ASOIAF (EN)
INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('A Game of Thrones','a-game-of-thrones',33.08,50,'assets/images/books/en/game-of-thrones.jpg',1,1),
('A Clash of Kings','a-clash-of-kings',37.99,50,'assets/images/books/en/clash-of-kings.jpg',1,0),
('A Storm of Swords','a-storm-of-swords',8.60,50,'assets/images/books/en/storm-of-swords.jpg',1,1),
('A Feast for Crows','a-feast-for-crows',48.65,50,'assets/images/books/en/feast-for-crows.jpg',1,0),
('A Dance with Dragons','a-dance-with-dragons',23.90,50,'assets/images/books/en/dance-with-dragons.jpg',1,1);

SET @p_got_en := (SELECT id FROM products WHERE slug='a-game-of-thrones' LIMIT 1);
SET @p_clash_en := (SELECT id FROM products WHERE slug='a-clash-of-kings' LIMIT 1);
SET @p_storm_en := (SELECT id FROM products WHERE slug='a-storm-of-swords' LIMIT 1);
SET @p_feast_en := (SELECT id FROM products WHERE slug='a-feast-for-crows' LIMIT 1);
SET @p_dance_en := (SELECT id FROM products WHERE slug='a-dance-with-dragons' LIMIT 1);

INSERT INTO books (product_id, work_id, editorial_id, lang, format, isbn, pages, edition, synopsis, published_year) VALUES
(@p_got_en,(SELECT id FROM book_works WHERE slug='game-of-thrones'),@ed_bantam,'en','hardcover','9780553103540',694,NULL,NULL,1996),
(@p_clash_en,(SELECT id FROM book_works WHERE slug='clash-of-kings'),@ed_bantam,'en','hardcover','9780553108033',768,NULL,NULL,1999),
(@p_storm_en,(SELECT id FROM book_works WHERE slug='storm-of-swords'),@ed_bantam,'en','hardcover','9780553106633',992,NULL,NULL,2000),
(@p_feast_en,(SELECT id FROM book_works WHERE slug='feast-for-crows'),@ed_bantam,'en','hardcover','9780553801507',784,NULL,NULL,2005),
(@p_dance_en,(SELECT id FROM book_works WHERE slug='dance-with-dragons'),@ed_bantam,'en','hardcover','9780553801477',1040,NULL,NULL,2011);

-- =========================
-- | Categorías (mapping)  |
-- =========================

INSERT INTO product_categories (product_id, category_id) VALUES
  (@p_m1, @cat_fantasy),
  (@p_m2, @cat_fantasy),
  (@p_m3, @cat_fantasy),

  (@p_fl1, @cat_fantasy),
  (@p_fl2, @cat_fantasy),
  (@p_fl3, @cat_fantasy),

  (@p_inst_es, @cat_thriller),
  (@p_inst_es, @cat_horror),

  (@p_notw_es, @cat_fantasy),
  (@p_notw_es, @cat_epic),

  (@p_eos_es, @cat_scifi),
  (@p_eos_es, @cat_space),

  (@p_lyndale_es, @cat_fantasy),

  (@p_islas_es, @cat_fantasy),
  (@p_islas_es, @cat_cosmere),

  (@p_trenza_es, @cat_fantasy),
  (@p_trenza_es, @cat_cosmere),

  (@p_heroes_es, @cat_fantasy),
  (@p_heroes_es, @cat_grimdark),

  (@p_bestia_es, @cat_thriller),
  (@p_bestia_es, @cat_historical),

  (@p_carl_es, @cat_scifi),
  (@p_carl_es, @cat_fantasy),

  (@p_wmf_es, @cat_fantasy),
  (@p_slow_es, @cat_fantasy),
  (@p_narrow_es, @cat_fantasy),

  (@p_got_es, @cat_fantasy),
  (@p_got_es, @cat_epic),
  (@p_clash_es, @cat_fantasy),
  (@p_clash_es, @cat_epic),
  (@p_storm_es, @cat_fantasy),
  (@p_storm_es, @cat_epic),
  (@p_feast_es, @cat_fantasy),
  (@p_feast_es, @cat_epic),
  (@p_dance_es, @cat_fantasy),
  (@p_dance_es, @cat_epic),

  -- EN mappings (en general las mismas categorías)
  (@p_isles_en, @cat_fantasy),
  (@p_isles_en, @cat_cosmere),

  (@p_trenza_en, @cat_fantasy),
  (@p_trenza_en, @cat_cosmere),

  (@p_devils_en, @cat_fantasy),
  (@p_devils_en, @cat_grimdark),

  (@p_heroes_en, @cat_fantasy),
  (@p_heroes_en, @cat_grimdark),

  (@p_inst_en, @cat_thriller),
  (@p_inst_en, @cat_horror),

  (@p_bestia_en, @cat_thriller),
  (@p_bestia_en, @cat_historical),

  (@p_carl_en, @cat_scifi),
  (@p_carl_en, @cat_fantasy),

  (@p_eos_en, @cat_scifi),
  (@p_eos_en, @cat_space),

  (@p_mistborn_en, @cat_fantasy),
  (@p_mistborn_en, @cat_cosmere),

  (@p_notw_en, @cat_fantasy),
  (@p_wmf_en, @cat_fantasy),
  (@p_slow_en, @cat_fantasy),
  (@p_narrow_en, @cat_fantasy),

  (@p_got_en, @cat_fantasy),
  (@p_got_en, @cat_epic),
  (@p_clash_en, @cat_fantasy),
  (@p_clash_en, @cat_epic),
  (@p_storm_en, @cat_fantasy),
  (@p_storm_en, @cat_epic),
  (@p_feast_en, @cat_fantasy),
  (@p_feast_en, @cat_epic),
  (@p_dance_en, @cat_fantasy),
  (@p_dance_en, @cat_epic)
ON DUPLICATE KEY UPDATE product_id = product_id;

-- =========================
-- | Packs (como tenías)   |
-- =========================

INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('Pack Mistborn (Trilogía)', 'pack-mistborn-trilogia', 64.90, 20, 'assets/images/packs/pack-mistborn.jpg', 1, 1);
SET @p_pack_mistborn := (SELECT id FROM products WHERE slug='pack-mistborn-trilogia' LIMIT 1);

INSERT INTO packs (product_id, description)
VALUES (@p_pack_mistborn, 'Trilogía Mistborn (1-3) de Brandon Sanderson.')
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO pack_items (pack_product_id, product_id, quantity) VALUES
  (@p_pack_mistborn, @p_m1, 1),
  (@p_pack_mistborn, @p_m2, 1),
  (@p_pack_mistborn, @p_m3, 1)
ON DUPLICATE KEY UPDATE quantity = VALUES(quantity);

INSERT INTO products (name, slug, price, stock, image_path, is_active, is_featured) VALUES
('Pack La Primera Ley (Trilogía)', 'pack-la-primera-ley-trilogia', 54.90, 20, 'assets/images/packs/pack-primera-ley.jpg', 1, 0);
SET @p_pack_firstlaw := (SELECT id FROM products WHERE slug='pack-la-primera-ley-trilogia' LIMIT 1);

INSERT INTO packs (product_id, description)
VALUES (@p_pack_firstlaw, 'Trilogía La Primera Ley (1-3) de Joe Abercrombie.')
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO pack_items (pack_product_id, product_id, quantity) VALUES
  (@p_pack_firstlaw, @p_fl1, 1),
  (@p_pack_firstlaw, @p_fl2, 1),
  (@p_pack_firstlaw, @p_fl3, 1)
ON DUPLICATE KEY UPDATE quantity = VALUES(quantity);
