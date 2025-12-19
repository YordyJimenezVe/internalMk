<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
  </a>
</p>

<h1 align="center">Sistema de Inventario Internal Maikel Cars</h1>

<p align="center">
    <img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel">
    <img src="https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js" alt="Vue.js">
    <img src="https://img.shields.io/badge/Inertia.js-1.x-9553E9?style=for-the-badge&logo=inertia" alt="Inertia">
    <img src="https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css" alt="Tailwind">
</p>

## 📋 Sobre el Proyecto

**Internal Maikel Cars** es una plataforma integral desarrollada para la gestión y control de inventario automotriz. Diseñado para optimizar el flujo de trabajo interno, el sistema permite el seguimiento detallado de:

- **Motores y Cajas**
- **Cámaras y Autopartes**
- **Facturación y Ventas**
- **Reportes Gerenciales**

El sistema centraliza la información operativa, permitiendo un control preciso desde la recepción de la mercancía hasta su venta final.

---

## 🚀 Características Principales

### 📦 Gestión de Inventario
- Control detallado de partidas (Motores, Cajas, Accesorios).
- Asignación de Códigos de Inventario únicos.
- Escaneo de códigos QR y de Barras para búsqueda rápida.

### 💰 Facturación y Ventas
- Generación de solicitudes de facturación.
- Estatus en tiempo real (Disponible / Apartado / Vendido).
- Cálculo automático de totales en divisas ($).

### 📊 Reportes Inteligentes
- **Exportación a Excel y PDF** con estilos corporativos.
- Filtros avanzados por Fecha, Estatus y Tipo.
- Resúmenes financieros y conteo de stock.

---

## 🛠️ Tecnologías Utilizadas

Este proyecto utiliza un stack moderno y robusto:

- **Backend**: [Laravel 10](https://laravel.com/)
- **Frontend**: [Vue.js 3](https://vuejs.org/) con [Inertia.js](https://inertiajs.com/)
- **Estilos**: [Tailwind CSS](https://tailwindcss.com/)
- **Base de Datos**: MySQL
- **Autenticación**: Laravel Jetstream / Fortify

---

## ⚙️ Instalación y Configuración

Sigue estos pasos para desplegar el proyecto en tu entorno local:

1.  **Clonar el repositorio**:
    ```bash
    git clone https://github.com/YordyJimenezVe/internalMk.git
    cd internalMk
    ```

2.  **Instalar dependencias Backend**:
    ```bash
    composer install
    ```

3.  **Instalar dependencias Frontend**:
    ```bash
    npm install
    npm run build
    ```

4.  **Configurar entorno**:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Configura tu base de datos en el archivo `.env`.*

5.  **Migrar base de datos**:
    ```bash
    php artisan migrate
    ```

6.  **Iniciar servidor**:
    ```bash
    php artisan serve
    ```

---

## 👨‍💻 Desarrollador

Este proyecto ha sido desarrollado en su totalidad por:

**Yordy Jiménez**  
📧 [yordyalejandro13@gmail.com](mailto:yordyalejandro13@gmail.com)

---

<p align="center">
    &copy; 2024 - 2025 Todos los derechos reservados.
</p>
