# 🎫 Ticketing App - Laravel

Sistema de gestión de tickets para soporte técnico interno desarrollado con **Laravel**.

Permite gestionar:

* Empleados (reporters)
* Agentes
* Tickets
* Categorías y prioridades
* Historial de estados
* Mensajes internos y públicos

---

## 🛠 Requisitos

* PHP >= 8.2
* Composer
* MySQL / MariaDB
* Laravel 11+
* Extensión PDO habilitada

---

## 🚀 Instalación

### 1️⃣ Clonar el repositorio

```bash
git clone https://github.com/AdrianCalizayaGuerrero/ticketing-app.git
```

### 2️⃣ Entrar al proyecto

```bash
cd ticketing-app
```

### 3️⃣ Instalar dependencias

```bash
composer install
```

### 4️⃣ Configurar variables de entorno

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

### 5️⃣ Generar clave de aplicación

```bash
php artisan key:generate
```

---

### 6️⃣ Ejecutar migraciones y seeders

Este proyecto utiliza UUID como claves primarias.

```bash
php artisan migrate --seed
```

Esto generará:

* Personas
* Empleados
* Agentes
* Categorías
* Prioridades
* Tickets
* Historial de estados
* Mensajes relacionados

---

### 7️⃣ Iniciar el servidor

```bash
php artisan serve
```

El proyecto estará disponible en:

```
http://127.0.0.1:8000
```

---

## 🧪 Probar datos generados

Puedes usar Tinker:

```bash
php artisan tinker
```

Ejemplo:

```php
App\Models\Ticked::count();
App\Models\Message::count();
```

---

## 🏗 Arquitectura

* UUID como claves primarias
* Factories y Seeders coherentes
* Relaciones Eloquent bien definidas
* Enum para estados de ticket
* Separación entre Person, Employee y Agent

---

## 📌 Notas

Si necesitas reiniciar completamente la base de datos:

```bash
php artisan migrate:fresh --seed
```

---

Desarrollado por **Adrian Calizaya**
