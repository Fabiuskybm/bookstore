<?php

// ======================
// |  Gobal App Config  |
// ======================


// Patrón para validar nombres de usuario
const NAME_PATTERN = '/^[\p{L}\p{M}\s\'-]{2,50}$/u';


// Roles
const ROLE_ADMIN = 'admin';
const ROLE_USER = 'user';


// Credenciales de prueba
const AUTH_ADMIN_USER = 'admin';
const AUTH_PASS = '1234';


// Rutas al catálogo de libros (JSON) por idioma.
const BOOKS_DATA_DIR = __DIR__ . '/../Book/data';

const BOOKS_FILE_ES = BOOKS_DATA_DIR . '/books_es.json';
const BOOKS_FILE_EN = BOOKS_DATA_DIR . '/books_en.json';
