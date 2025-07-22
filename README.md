# 🍎 Frutiflow - Sistema de Gestión de Inventario de Frutas

Sistema completo de gestión de inventario de frutas desarrollado con Laravel 11 y Filament 3, implementando la lógica PEPS (Primero en Entrar, Primero en Salir) para el control de stock, con soporte completo para pesos mexicanos y dashboard con gráficos en tiempo real.

## ✨ Características Principales

- **🔄 Gestión de Inventario PEPS**: Control automático de stock con lógica "Primero en Entrar, Primero en Salir"
- **📊 Dashboard con Gráficos**: Widgets interactivos con métricas en tiempo real
- **💰 Soporte para Pesos Mexicanos**: Formato de moneda MXN con locale mexicano
- **⏰ Control de Vencimientos**: Seguimiento de fechas de vencimiento y alertas
- **🏢 Gestión Completa**: Clientes, proveedores, productos, compras, ventas y gastos
- **👥 Sistema de Roles**: Control de acceso granular por rol de usuario
- **🎨 Panel Administrativo**: Interfaz moderna con Filament 3
- **📈 Reportes Visuales**: Análisis gráfico de ventas, stock y distribución

## 🛠️ Tecnologías Utilizadas

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend Admin**: Filament 3 con Widgets y Charts
- **Base de Datos**: SQLite
- **Autenticación**: Laravel Sanctum con roles
- **UI Components**: Blade + Livewire + Chart.js
- **Localización**: Español México (es_MX)
- **Moneda**: Peso Mexicano (MXN)

## 📊 Dashboard y Widgets

### 🎯 Widgets Implementados

1. **ProductosOverviewWidget** - Estadísticas Generales
   - Total de productos (activos/inactivos)
   - Stock total y disponible
   - Productos sin stock
   - Valor total del inventario en MXN
   - Margen de ganancia promedio

2. **ProductosStockChart** - Gráfico de Barras
   - Top 10 productos con mayor stock
   - Colores dinámicos por nivel de inventario
   - Formato con unidades específicas

3. **ProductosGrupoChart** - Gráfico Circular
   - Distribución de productos por grupos
   - Vista porcentual de categorías
   - Colores diferenciados

4. **InventarioValorChart** - Gráfico Doughnut
   - Valor del inventario por grupo en MXN
   - Formato de moneda en tooltips
   - Vista financiera del stock

### 🎨 Páginas del Dashboard

- **`/admin/productos/dashboard`** - Dashboard dedicado con todos los widgets
- **`/admin/productos`** - Lista con widgets integrados en header/footer
- **Diseño responsivo** - Se adapta a móviles y escritorio
- **Actualización en tiempo real** - Datos siempre actualizados

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
├── cast: precio_compra_referencia, precio_venta_sugerido (decimal)
├── accessors: precio_compra_formatted, precio_venta_formatted

Compra
├── belongsTo: Proveedor, User, Producto
├── hasMany: Inventario

Venta
├── belongsTo: Cliente, User, Producto

Inventario (PEPS)
├── belongsTo: Producto, Compra
├── attributes: lote, fecha_ingreso, fecha_vencimiento, estado
```

### 🏗️ Arquitectura de Archivos

```
app/
├── Filament/Admin/Resources/
│   ├── ProductoResource.php
│   └── ProductoResource/
│       ├── Pages/
│       │   ├── ListProductos.php
│       │   ├── CreateProducto.php
│       │   ├── EditProducto.php
│       │   └── ProductosDashboard.php
│       └── Widgets/
│           ├── ProductosOverviewWidget.php
│           ├── ProductosStockChart.php
│           ├── ProductosGrupoChart.php
│           └── InventarioValorChart.php
├── Helpers/
│   └── CurrencyHelper.php
└── Models/
    ├── Producto.php (con accessors de formato MXN)
    ├── Cliente.php
    ├── Proveedor.php
    ├── Compra.php
    ├── Venta.php
    ├── Inventario.php
    └── User.php

database/
├── migrations/ (11 migraciones optimizadas)
└── seeders/
    ├── DatabaseSeeder.php
    └── ProductoSeeder.php (10 productos ejemplo)

resources/views/filament/admin/resources/
└── producto-resource/pages/
    └── productos-dashboard.blade.php
```

## 🚀 Instalación y Uso

### 📋 Requisitos Previos
- PHP 8.2+
- Composer
- Node.js (para assets)
- SQLite habilitado

### ⚡ Instalación Rápida

```bash
# Clonar repositorio
git clone [tu-repo-url] frutiflow
cd frutiflow

# Instalar dependencias
composer install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Ejecutar migraciones con datos de ejemplo
php artisan migrate:fresh --seed

# Iniciar servidor
php artisan serve
```

### 🔐 Credenciales por Defecto
- **Email**: admin@frutiflow.com
- **Contraseña**: password123
- **Panel Admin**: `http://localhost:8000/admin`
- **Dashboard**: `http://localhost:8000/admin/productos/dashboard`

### 🌐 URLs Principales
- **Login**: `http://localhost:8000/admin/login`
- **Productos**: `http://localhost:8000/admin/productos`
- **Dashboard**: `http://localhost:8000/admin/productos/dashboard`
- **Crear Producto**: `http://localhost:8000/admin/productos/create`

## 💰 Sistema de Moneda Mexicana

### 🇲🇽 Configuración Regional
- **Moneda**: Peso Mexicano (MXN)
- **Locale**: Español México (es_MX)
- **Zona Horaria**: America/Mexico_City
- **Formato**: $1,234.56 MXN

### 🔧 CurrencyHelper
```php
// Formateo automático
CurrencyHelper::format(1234.56) // "$1,234.56"
CurrencyHelper::formatWithCurrency(1234.56) // "$1,234.56 MXN"

// Obtener configuración
CurrencyHelper::getCurrency() // "MXN"
CurrencyHelper::getLocale() // "es_MX"
CurrencyHelper::getCurrencySymbol() // "$"
```

## 📊 Funcionalidades PEPS

### 🔄 Flujo Automático
1. **Entrada (Compras)**:
   - Cada compra genera lotes con fecha de ingreso
   - Asignación automática de fechas de vencimiento
   - Cálculo de costo promedio PEPS

2. **Salida (Ventas)**:
   - Las ventas consumen primero el stock más antiguo
   - Actualización automática de inventario
   - Control de disponibilidad por lotes

3. **Control de Vencimientos**:
   - Alertas automáticas de productos próximos a vencer
   - Estados: disponible, reservado, vencido
   - Reportes de mermas y pérdidas

## 🎨 Características de UI/UX

### 🌈 Sistema de Colores Inteligentes
- **Verde**: Stock alto (>50), margen alto (≥30%)
- **Naranja**: Stock medio (20-50), margen medio (15-29%)
- **Rojo**: Stock bajo (<20), margen bajo (<15%)
- **Gris**: Sin datos o inactivo

### 📱 Diseño Responsivo
- **Desktop**: Grid de 2 columnas para gráficos
- **Tablet**: Layout adaptativo
- **Mobile**: Vista vertical optimizada

### ⚡ Interactividad
- **Tooltips informativos** en todos los gráficos
- **Filtros dinámicos** en tablas
- **Búsqueda en tiempo real**
- **Ordenamiento por columnas**

## 🔧 Comandos de Desarrollo

```bash
# Desarrollo
php artisan serve                    # Iniciar servidor
php artisan migrate:fresh --seed     # Recrear BD con datos

# Cache y optimización
php artisan optimize:clear           # Limpiar todos los cachés
php artisan config:clear            # Cache de configuración
php artisan filament:cache-components # Cache de componentes Filament

# Base de datos
php artisan migrate                  # Ejecutar migraciones
php artisan db:seed --class=ProductoSeeder # Sembrar productos

# Debugging
php artisan route:list               # Ver todas las rutas
php artisan queue:work              # Procesar colas (si aplica)
```

## 📈 Métricas y KPIs

El dashboard proporciona las siguientes métricas clave:

### 📊 Indicadores Principales
- **Total de productos** con breakdown activos/inactivos
- **Stock total** en unidades específicas (cajas, kg, etc.)
- **Valor del inventario** en pesos mexicanos
- **Margen de ganancia promedio** con semáforo de colores
- **Productos sin stock** para reabastecimiento

### 📈 Visualizaciones
- **Gráfico de barras**: Top 10 productos por stock
- **Gráfico circular**: Distribución por grupos de frutas
- **Gráfico doughnut**: Valor económico por grupo
- **Estadísticas cards**: KPIs principales con iconos

## 🤝 Contribución y Desarrollo

### 🔀 Historial de Commits
```bash
git log --oneline
# 83e0ae3 feat: Implementar sistema completo de widgets y gráficos para dashboard
# b1e60d9 feat: Implementar sistema completo de gestión de productos con pesos mexicanos
# 7c33c6e Commit inicial: Frutiflow - Sistema de Gestión de Inventario con lógica PEPS
```

### 🚀 Roadmap
- [ ] Módulo de Compras con integración PEPS
- [ ] Módulo de Ventas con consumo automático
- [ ] Alertas de vencimiento por email
- [ ] Reportes PDF personalizables
- [ ] API REST para integraciones
- [ ] App móvil para inventario

---

## 📄 Licencia

Este proyecto está bajo la licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 👨‍💻 Desarrollado con

- **Laravel 11** - Framework PHP moderno
- **Filament 3** - Panel administrativo elegante
- **Chart.js** - Gráficos interactivos
- **Tailwind CSS** - Diseño responsive
- **Livewire** - Componentes reactivos

---

**🍎 Frutiflow** - *Sistema profesional de gestión de inventario de frutas con tecnología de vanguardia*
