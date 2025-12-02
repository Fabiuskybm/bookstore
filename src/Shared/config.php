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
const AUTH_ADMIN_PASS = 'admin123';

const AUTH_USER_USER = 'user';
const AUTH_USER_PASS = 'user123';


// Ruta al catálogo de libros (JSON).
const BOOKS_FILE = __DIR__ . '/../Book/data/books.json';