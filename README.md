# 🍎 Frutiflow - Sistema de Gestión de Inventario de Frutas

Sistema completo de gestión de inventario de frutas desarrollado con Laravel 11 y Filament 3, implementando la lógica PEPS (Primero en Entrar, Primero en Salir) para el control de stock.

## 🚀 Características Principales

- **Gestión de Inventario PEPS**: Control automático de stock con lógica "Primero en Entrar, Primero en Salir"
- **Control de Vencimientos**: Seguimiento de fechas de vencimiento y alertas
- **Gestión Completa**: Clientes, proveedores, productos, compras, ventas y gastos
- **Sistema de Roles**: Control de acceso granular por rol de usuario
- **Panel Administrativo**: Interfaz moderna con Filament 3
- **Reportes**: Análisis de ventas, compras y estado del inventario

## 🛠️ Tecnologías Utilizadas

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend Admin**: Filament 3
- **Base de Datos**: SQLite
- **Autenticación**: Laravel Sanctum
- **UI Components**: Blade + Livewire

## 📦 Estructura del Proyecto

### Modelos y Relaciones

```
User (Usuario)
├── belongsTo: Role
├── hasMany: Compra, Venta, Gasto

Role (Rol)
├── hasMany: User

Cliente
├── hasMany: Venta

Proveedor
├── hasMany: Compra

Producto
├── hasMany: Compra, Venta, Inventario

Compra
├── belongsTo: Proveedor, User, Producto
├── hasMany: Inventario

Venta
├── belongsTo: Cliente, User, Producto

Inventario (PEPS)
├── belongsTo: Producto, Compra
```

## 🚀 Instalación y Uso

### Credenciales por Defecto
- **Email**: admin@frutiflow.com
- **Contraseña**: password123
- **Panel Admin**: `http://localhost:8000/admin`
- **Login**: `http://localhost:8000/admin/login`
- **Ruta Raíz**: `http://localhost:8000/` (redirige automáticamente a `/admin`)

### Comandos Principales
```bash
# Iniciar servidor
php artisan serve

# Limpiar cachés
php artisan optimize:clear

# Recrear BD con datos
php artisan migrate:fresh --seed

# Ver estado de Git
git status

# Ver historial de commits
git log --oneline
```

## 📊 Funcionalidades PEPS

El sistema implementa automáticamente:
1. **Entrada**: Cada compra genera lotes con fecha de ingreso
2. **Salida**: Las ventas consumen primero el stock más antiguo  
3. **Control**: Alertas de vencimiento y gestión automática

---

Desarrollado con Laravel 11 + Filament 3

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
