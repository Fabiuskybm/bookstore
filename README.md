# Bookstore

Proyecto conjunto de 1ª Evaluación (DSW + DEW + DOR) para 2º DAW.  
Este repositorio contiene la base del proyecto, incluyendo:
- Servidor PHP con Apache (Docker)
- Entorno frontend con Webpack (JS + SASS)

---

## 🚀 Ejecutar servidor PHP (Apache)

Desde la raíz del proyecto:

```bash
docker-compose up
```

Una vez levantado, acceder en el navegador:

👉 http://localhost:8080

---

## 🎨 Configurar y compilar el frontend (Webpack + SASS)

Antes de compilar, es necesario instalar las dependencias necesarias.

### 1. Instalar dependencias (solo la primera vez)

Desde la carpeta `frontend`:

```bash
cd frontend
npm install --save-dev webpack webpack-cli
npm install --save-dev sass sass-loader css-loader style-loader
```

Esto instalará:
- Webpack
- Webpack CLI
- SASS
- Cargadores necesarios para procesar SASS y CSS

---

### 2. Compilación en modo desarrollo (watch)

Desde la carpeta `frontend`:

```bash
npm run dev
```

Webpack quedará escuchando cambios y generando automáticamente los assets en:

- `public/assets/js/`
- `public/assets/css/` *(cuando más adelante extraigamos el CSS físico)*

---

## 📦 Dependencias incluidas

Tras ejecutar los comandos anteriores, `package.json` contendrá automáticamente:

- webpack  
- webpack-cli  
- sass  
- sass-loader  
- css-loader  
- style-loader  

---

## 📂 Estructura inicial del proyecto

```
bookstore/
├── public/
│   └── index.php
├── frontend/
│   ├── src/
│   │   ├── js/app.js
│   │   └── styles/main.scss
│   ├── package.json
│   └── webpack.config.js
├── docker-compose.yml
├── .gitignore
└── README.md
```

---

## ✔️ Estado actual

Proyecto inicial configurado y funcional.  
Listo para comenzar a desarrollar la parte frontend y backend del proyecto.