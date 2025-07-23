# 🍎 Frutiflow - Sistema de Gestión de Inventario de Frutas

Sistema completo de gestión de inventario de frutas desarrollado con Laravel 11 y Filament 3, implementando la lógica PEPS (Primero en Entrar, Primero en Salir) para el control de stock, con sistema integral de control de créditos y dashboard con gráficos en tiempo real.

## ✨ Características Principales

- **🔄 Gestión de Inventario PEPS**: Control automático de stock con lógica "Primero en Entrar, Primero en Salir"
- **� Control de Créditos Integral**: Sistema completo de gestión crediticia con límites, pagos y morosidad
- **�📊 Dashboard con Widgets**: Múltiples widgets interactivos con métricas en tiempo real
- **💰 Soporte para Pesos Mexicanos**: Formato de moneda MXN con locale mexicano
- **⏰ Control de Vencimientos**: Seguimiento de fechas de vencimiento y alertas
- **🏢 Gestión Completa**: Clientes, proveedores, productos, compras, ventas y gastos
- **👥 Sistema de Roles**: Control de acceso granular por rol de usuario
- **🎨 Panel Administrativo**: Interfaz moderna con Filament 3
- **📈 Reportes Visuales**: Análisis gráfico de ventas, stock, créditos y cobranza

## 🛠️ Tecnologías Utilizadas

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend Admin**: Filament 3 con Widgets y Charts
- **Base de Datos**: SQLite
- **Autenticación**: Laravel Sanctum con roles
- **UI Components**: Blade + Livewire + Chart.js
- **Localización**: Español México (es_MX)
- **Moneda**: Peso Mexicano (MXN)

## 📊 Dashboard y Widgets

### � Módulo de Productos
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

### 💳 Módulo de Créditos y Clientes
1. **EstadisticasCreditoWidget** - Métricas de Crédito
   - Deuda total por cobrar en MXN
   - Clientes con deuda activa
   - Clientes bloqueados por exceder límite
   - Límites de crédito próximos a agotar (≥80%)
   - Pagos recibidos del mes actual
   - Promedio de días de crédito otorgados

2. **ClientesMayorDeudaWidget** - Top 10 Deudores
   - Lista de clientes con mayor saldo pendiente
   - Información de contacto y documento
   - Porcentaje de crédito utilizado
   - Estado del crédito con badges coloridos
   - Formato de moneda mexicana

3. **PagosVencidosWidget** - Control de Morosidad
   - Clientes con pagos vencidos
   - Cálculo automático de días vencidos
   - Información de contacto para cobranza
   - Estados de alerta visual

### 💰 Módulo de Pagos
1. **EvolucionPagosChart** - Evolución Temporal
   - Gráfico de líneas de últimos 6 meses
   - Pagos reales vs metas de cobranza
   - Formato de moneda en tooltips
   - Actualización automática cada 30s

## 💳 Gestión de Créditos y Control Financiero

### �️ Sistema de Créditos Integral
- **Límites de Crédito**: Configuración por cliente en MXN
- **Días de Crédito**: Control de plazos de pago (1-365 días)
- **Estados de Crédito**: Activo, Suspendido, Bloqueado
- **Saldo Pendiente**: Cálculo automático en tiempo real
- **Control de Morosidad**: Detección automática de pagos vencidos

### 💰 Gestión de Pagos
- **Registro de Pagos**: Múltiples tipos (pago, anticipo, abono)
- **Métodos de Pago**: Efectivo, transferencia, cheque, tarjeta
- **Trazabilidad**: Usuario que registra, fecha y observaciones
- **Actualización Automática**: Observer para actualizar saldos
- **Histórico Completo**: Registro detallado de todos los movimientos

### 📊 Widgets de Crédito en Tiempo Real
- **Métricas Financieras**: Deuda total, pagos del mes, promedios
- **Alertas Visuales**: Semáforos por estado de crédito
- **Top Deudores**: Lista de clientes con mayor saldo pendiente
- **Control de Morosidad**: Seguimiento de pagos vencidos
- **Evolución Temporal**: Gráficos de tendencias de cobranza

## 🎨 Páginas del Dashboard

### 📍 Rutas Principales
- **`/admin/productos`** - Dashboard de productos con widgets
- **`/admin/clientes`** - Dashboard de créditos y cobranza
- **`/admin/pago-clientes`** - Dashboard de evolución de pagos
- **Diseño responsivo** - Se adapta a móviles y escritorio
- **Actualización en tiempo real** - Polling automático de datos

## 📦 Estructura del Proyecto

### Modelos y Relaciones

```
User (Usuario)
├── belongsTo: Role
├── hasMany: Compra, Venta, Gasto, PagoCliente

Role (Rol)
├── hasMany: User

Cliente
├── hasMany: Venta, PagoCliente
├── attributes: limite_credito, dias_credito, saldo_pendiente, estado_credito
├── methods: puedeComprarMonto(), actualizarEstadoCredito(), getPorcentajeCreditoUsado()

PagoCliente
├── belongsTo: Cliente, User
├── types: pago, anticipo, abono
├── methods: efectivo, transferencia, cheque, tarjeta
├── observer: PagoClienteObserver (actualiza saldos automáticamente)

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
│   ├── ClienteResource.php
│   ├── PagoClienteResource.php
│   ├── ProductoResource/Widgets/
│   │   ├── ProductosOverviewWidget.php
│   │   ├── ProductosStockChart.php
│   │   ├── ProductosGrupoChart.php
│   │   └── InventarioValorChart.php
│   ├── ClienteResource/Widgets/
│   │   ├── EstadisticasCreditoWidget.php
│   │   ├── ClientesMayorDeudaWidget.php
│   │   └── PagosVencidosWidget.php
│   └── PagoClienteResource/Widgets/
│       └── EvolucionPagosChart.php
├── Helpers/
│   └── CurrencyHelper.php
├── Observers/
│   └── PagoClienteObserver.php
└── Models/
    ├── Producto.php (con accessors de formato MXN)
    ├── Cliente.php (con sistema de créditos)
    ├── PagoCliente.php (con trazabilidad)
    ├── Proveedor.php
    ├── Compra.php
    ├── Venta.php
    ├── Inventario.php
    └── User.php
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

### 📊 Dashboard de Productos
- **Total de productos** con breakdown activos/inactivos
- **Stock total** en unidades específicas (cajas, kg, etc.)
- **Valor del inventario** en pesos mexicanos
- **Margen de ganancia promedio** con semáforo de colores
- **Productos sin stock** para reabastecimiento

### 💳 Dashboard de Créditos
- **Deuda total por cobrar** con formato MXN
- **Clientes con saldo pendiente** y estado del crédito
- **Límites próximos a agotar** (≥80% utilizado)
- **Pagos vencidos** con días de retraso
- **Pagos del mes** vs metas de cobranza
- **Promedio de días de crédito** otorgados

### 📈 Visualizaciones Avanzadas
- **Gráfico de barras**: Top 10 productos por stock
- **Gráfico circular**: Distribución por grupos de frutas
- **Gráfico doughnut**: Valor económico por grupo
- **Gráfico de líneas**: Evolución de pagos y metas
- **Estadísticas cards**: KPIs principales con iconos

### 🎯 Indicadores de Color Inteligentes
- **🟢 Verde**: Estados saludables (< 70% crédito usado)
- **🟡 Amarillo**: Advertencias (70-89% crédito usado)  
- **🔴 Rojo**: Crítico (≥90% crédito usado o bloqueado)
- **⚪ Gris**: Sin datos o estados neutros

## 💰 Helper de Moneda Mexicana

```php
use App\Helpers\CurrencyHelper;

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
- **Verde**: Stock alto (>50), margen alto (≥30%), crédito saludable
- **Naranja**: Stock medio (20-50), margen medio (15-29%), advertencias
- **Rojo**: Stock bajo (<20), margen bajo (<15%), crítico
- **Gris**: Sin datos o inactivo

### 📱 Diseño Responsivo
- **Desktop**: Grid de múltiples columnas para widgets
- **Tablet**: Layout adaptativo
- **Mobile**: Vista vertical optimizada

### ⚡ Interactividad
- **Tooltips informativos** en todos los gráficos
- **Filtros dinámicos** en tablas
- **Búsqueda en tiempo real**
- **Polling automático** para datos actualizados
- **Badges de estado** con colores semánticos

## 🔧 Comandos de Desarrollo

```bash
# Desarrollo
php artisan serve                    # Iniciar servidor
php artisan migrate:fresh --seed     # Recrear BD con datos de prueba

# Seeders específicos
php artisan db:seed --class=ClienteSeeder      # Datos de clientes
php artisan db:seed --class=PagoClienteSeeder  # Datos de pagos

# Cache y limpieza
php artisan config:clear             # Limpiar configuración
php artisan view:clear              # Limpiar vistas
php artisan filament:clear-cached-components  # Limpiar widgets

## 🤝 Contribución y Desarrollo

## 🤝 Contribución y Desarrollo

### 🔀 Historial de Commits
```bash
git log --oneline
# feat: Implementar sistema completo de créditos con widgets dashboard
# feat: Implementar sistema completo de widgets y gráficos para dashboard
# feat: Implementar sistema completo de gestión de productos con pesos mexicanos
# Commit inicial: Frutiflow - Sistema de Gestión de Inventario con lógica PEPS
```

### 🚀 Roadmap
- [x] **Sistema de Créditos Completo** - Control de límites, pagos y morosidad
- [x] **Dashboard con Widgets** - Métricas visuales en tiempo real  
- [x] **Gestión de Productos** - CRUD completo con formato MXN
- [ ] **Módulo de Compras** - Integración PEPS con proveedores
- [ ] **Módulo de Ventas** - Consumo automático de inventario
- [ ] **Alertas de Vencimiento** - Notificaciones por email
- [ ] **Reportes PDF** - Documentos personalizables
- [ ] **API REST** - Endpoints para integraciones
- [ ] **App Móvil** - Cliente para gestión de inventario

### 📊 Estado Actual del Proyecto
- ✅ **Gestión de Clientes con Créditos**: 100% completo
- ✅ **Sistema de Pagos**: 100% completo  
- ✅ **Widgets de Dashboard**: 100% completo
- ✅ **Gestión de Productos**: 100% completo
- ✅ **Helper de Moneda MXN**: 100% completo
- 🔄 **Integración PEPS**: En desarrollo
- 🔄 **Módulo de Ventas**: Planificado

---

## 📄 Licencia

Este proyecto está bajo la licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 👨‍💻 Desarrollado con

- **Laravel 11** - Framework PHP moderno y robusto
- **Filament 3** - Panel administrativo elegante y funcional
- **Chart.js** - Gráficos interactivos y responsivos
- **Tailwind CSS** - Diseño moderno y responsive
- **Livewire** - Componentes reactivos sin JavaScript
- **SQLite** - Base de datos ligera y eficiente
- **Observer Pattern** - Actualización automática de datos

## 🎯 Características Técnicas Avanzadas

### 🔐 Seguridad y Validación
- **Validación de Créditos**: Límites automáticos por cliente
- **Estados de Cuenta**: Trazabilidad completa de movimientos
- **Control de Acceso**: Sistema de roles granular
- **Observers**: Actualizaciones automáticas de saldos

### ⚡ Rendimiento
- **Polling Inteligente**: Actualización cada 10-30 segundos
- **Caché de Componentes**: Optimización de widgets
- **Consultas Optimizadas**: Relaciones Eloquent eficientes
- **Formato Lazy**: Carga diferida de datos pesados

### 🎨 Experiencia de Usuario
- **Dashboard Intuitivo**: Métricas visuales claras
- **Responsive Design**: Adaptable a todos los dispositivos
- **Feedback Visual**: Estados con colores semánticos
- **Navegación Fluida**: Interfaz coherente y moderna

---

**🍎 Frutiflow** - *Sistema profesional de gestión de inventario de frutas con control de créditos y tecnología de vanguardia*
