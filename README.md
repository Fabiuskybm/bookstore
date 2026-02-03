# 📚 Bookstore — Proyecto Full Stack (2º DAW)

Proyecto conjunto de la **1ª Evaluación (DSW + DEW + DOR)**.
Incluye backend en **PHP 8 + Apache (Docker)** y frontend modular con **Webpack, ES Modules y SASS**.

---

## 🚀 Puesta en marcha

### Backend (Docker)

```
docker-compose up
```

Abrir en el navegador:  
👉 **http://localhost:8080**

```yml
services:
  web:
    image: php:8.2-apache
    container_name: bookstore-web
    ports:
      - "8080:80"
    volumes:
      - ./public:/var/www/html
      - ./src:/var/www/src
    working_dir: /var/www/html
```

---

## 🎨 Frontend (Webpack + SASS)

### Instalar dependencias

```
cd frontend
npm install
```

### Modo desarrollo (watch)

```
npm run dev
```

### Build final

```
npm run build
```

Genera los assets en:

- `public/assets/js/main.js`

---

## 🗂️ Estructura del proyecto

```
bookstore/
├── compose.yml
├── public/
├── frontend/
│   ├── src/js/...
│   └── src/styles/...
└── src/ (PHP)
    ├── Auth/
    ├── Book/
    ├── Cart/
    ├── Home/
    ├── Preference/
    ├── Wishlist/
    ├── Admin/
    └── Shared/
```

---

## 🔧 Funcionalidades principales

### Frontend
- Webpack + ES Modules
- SASS modular (ITCSS + BEM)
- Carrusel de libros destacados
- Badge dinámico del carrito
- Dropdowns
- Botón scroll-to-top
- Preferencias (tema + items por página)
- Wishlist con selección múltiple

### Backend
- Carga de libros desde base de datos
- Wishlist almacenada en base de datos
- Preferencias guardadas en cookie
- Sistema básico de plantillas
- Traducciones (`i18n/es.json` + `i18n/en.json`)
- Carrito gestionado en frontend
- Módulo de autenticación
- Área de administración con página *Under Construction*

---

## 📌 Estado del proyecto

La aplicación es **navegable y funcional** a nivel de home, carrito, wishlist, preferencias, y estructura general.  
Pendiente: completar registro/login y contenido real en la sección de administración.
