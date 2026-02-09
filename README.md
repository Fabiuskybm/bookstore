# Bookstore

Aplicación web de librería desarrollada con arquitectura **PHP modular + frontend moderno con Webpack/SASS**, orientada a catálogo de libros, packs, wishlist, carrito, autenticación y valoraciones.

## Descripción del proyecto

Este proyecto implementa una tienda de libros con renderizado server-side en PHP y una capa de interacción en JavaScript (incluyendo un módulo de rating en React). La aplicación está organizada por dominios (`Auth`, `Book`, `Wishlist`, `Pack`, `Product`, etc.) y utiliza MySQL como persistencia principal.

Incluye:
- catálogo con detalle de producto;
- wishlist persistida por usuario;
- packs de productos;
- preferencias de interfaz (idioma/tema);
- autenticación básica con roles (`user`, `admin`);
- área de administración (en construcción);
- soporte i18n (`es`/`en`).

## Stack tecnológico

### Backend
- **PHP 8.2** (Apache)
- **MySQL 8**
- Acceso a datos con **PDO**
- Arquitectura modular con controladores, servicios, repositorios y vistas por dominio

### Frontend
- **JavaScript ES Modules**
- **React 18** (módulo de rating)
- **Webpack 5 + Babel**
- **SASS** (estructura por `core`, `layout`, `components`, `pages`)

### Infraestructura
- **Docker / Docker Compose** para entorno completo (`web` + `db`)

## Requisitos

- Docker y Docker Compose
- Node.js 18+ y npm (para compilar frontend)

## Instalación y ejecución

### 1) Clonar el repositorio

```bash
git clone <url-del-repo>
cd bookstore
```

### 2) Levantar backend y base de datos

```bash
docker compose up --build
```

La aplicación quedará disponible en:
- **http://localhost:8080**

> Nota: el esquema SQL se carga automáticamente al iniciar MySQL por primera vez mediante `database/schema.sql`.

### 3) Instalar dependencias del frontend

```bash
cd frontend
npm install
```

### 4) Compilar assets

Desarrollo (watch):
```bash
npm run dev
```

Build puntual:
```bash
npm run build
```

El bundle se genera en `public/assets/js/main.js`.

## Credenciales de prueba

El esquema inicial crea un usuario administrador:
- **email**: `admin@bookstore.com`
- **usuario**: `admin`
- **password**: configurada en el hash inicial de la base de datos

Si necesitas una contraseña conocida para pruebas, actualízala en MySQL o crea un usuario nuevo desde la interfaz de registro.

## Estructura del proyecto

```text
bookstore/
├── compose.yml
├── Dockerfile
├── database/
│   └── schema.sql
├── public/
│   ├── index.php
│   └── assets/
├── src/
│   ├── Admin/ Auth/ Book/ Cart/ Home/ Pack/ Preference/ Product/ Rating/ Wishlist/
│   └── Shared/
└── frontend/
    ├── src/js/
    ├── src/styles/
    ├── package.json
    └── webpack.config.js
```

## Scripts útiles

Desde `frontend/`:
- `npm run dev` → compilación en modo watch
- `npm run build` → compilación manual

Desde raíz del proyecto:
- `docker compose up --build` → arranque completo
- `docker compose down` → parada de contenedores

## Estado actual

El proyecto está funcional para navegación principal, catálogo, wishlist, carrito y preferencias. Algunas secciones (como parte del área admin) están señaladas como en progreso.

## Licencia

Uso académico / formativo (2º DAW).
