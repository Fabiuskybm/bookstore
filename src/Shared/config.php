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


// Idiomas soportados por el catálogo y la interfaz
const SUPPORTED_LANGUAGES = ['es', 'en'];


// ------------------
//  Database (MySQL)
// ------------------
const DB_HOST = 'db';
const DB_PORT = 3306;
const DB_NAME = 'bookstore';
const DB_USER = 'root';
const DB_PASSWORD = '1234';
const DB_CHARSET = 'utf8mb4';
