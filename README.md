# 🎫 Ticketing App - Laravel

Sistema de gestión de tickets para soporte técnico interno desarrollado con **Laravel**.

Permite gestionar:

- Empleados (Reporters)
- Agentes
- Tickets
- Categorías
- Prioridades
- Estados de ticket
- Historial de estados
- Mensajes internos y públicos
- Autenticación de usuarios

---

# 🛠 Requisitos

Antes de instalar el proyecto asegúrate de tener instalado:

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL / MariaDB
- Laravel 11+
- Extensión PDO habilitada

---

# 🚀 Instalación

## 1️⃣ Clonar el repositorio

```bash
git clone https://github.com/AdrianCalizayaGuerrero/ticketing-app.git
```

---

## 2️⃣ Entrar al proyecto

```bash
cd ticketing-app
```

---

## 3️⃣ Instalar dependencias de PHP

```bash
composer install
```

---

## 4️⃣ Instalar dependencias de frontend (Vite)

```bash
npm install
```

---

## 5️⃣ Configurar variables de entorno

```bash
cp .env.example .env
```

Editar el archivo `.env` y configurar la base de datos:

```
DB_DATABASE=ticketing_db
DB_USERNAME=root
DB_PASSWORD=
```

⚠️ Asegúrate de haber creado la base de datos previamente en MySQL.

---

## 6️⃣ Generar clave de aplicación

```bash
php artisan key:generate
```

---

# 🔐 Instalación de Sanctum

Este proyecto utiliza **Laravel Sanctum** para la autenticación.

### Instalar Sanctum

```bash
composer require laravel/sanctum
```

### Publicar configuración

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

Sanctum permitirá gestionar autenticación segura mediante **tokens o sesiones**.

---

# ⚠️ IMPORTANTE SOBRE MIGRACIONES

La tabla **roles** es la **última migración del proyecto**, pero **debe ejecutarse antes que otras tablas que dependen de ella**.

Si ejecutas todas las migraciones normalmente puede generar errores de claves foráneas.

### Solución recomendada

Ejecutar primero la migración de roles:

```bash
php artisan migrate --path=database/migrations/xxxx_xx_xx_create_roles_table.php
```

Luego ejecutar el resto (ejecutara tambien la migracion de sanctum):

```bash
php artisan migrate
```

---

# 🌱 Ejecutar Seeders

Este proyecto utiliza **seeders para generar datos iniciales del sistema**.

```bash
php artisan db:seed
```

Esto generará datos de prueba como:

- Personas
- Empleados
- Agentes
- Roles
- Categorías
- Prioridades
- Tickets
- Historial de estados
- Mensajes

---

# ⚡ Compilar assets con Vite

Este proyecto usa **Vite para manejar CSS y JavaScript**.

Para iniciar el servidor de desarrollo:

```bash
npm run dev
```

Para compilar para producción:

```bash
npm run build
```

---

# ▶️ Iniciar el servidor

```bash
php artisan serve
```

El proyecto estará disponible en:

```
http://127.0.0.1:8000
```

---

# 🧪 Probar datos generados

Puedes usar **Tinker** para verificar los registros generados:

```bash
php artisan tinker
```

Ejemplo:

```php
App\Models\Ticket::count();
App\Models\Message::count();
```

---

# 🏗 Arquitectura

El proyecto sigue una arquitectura basada en buenas prácticas de Laravel:

- UUID como claves primarias
- Factories y Seeders coherentes
- Relaciones Eloquent bien definidas
- Enum para estados de ticket
- Separación entre Person, Employee y Agent
- Sistema de roles
- Autenticación con Sanctum
- Assets manejados con Vite

---

# 📌 Reiniciar base de datos

Si necesitas reiniciar completamente la base de datos:

```bash
php artisan migrate:fresh --seed
```

---

# 👨‍💻 Desarrollado por

**Los Pasageros**
