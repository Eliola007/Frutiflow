# 🍎 Frutiflow - Sistema de Gestión de Inventario de Frutas

Sistema completo de gestión de inventario de frutas desarrollado con Laravel 11 y Filament 3, implementando la lógica PEPS (Primero en Entrar, Primero en Salir) para el control de stock, con sistema integral de control de créditos, gestión de compras multi-producto y dashboard con gráficos en tiempo real.

## ✨ Características Principales

- **🔄 Gestión de Inventario PEPS**: Control automático de stock con lógica "Primero en Entrar, Primero en Salir"
- **🛒 Sistema de Compras Avanzado**: Compras multi-producto con Repeater para múltiples items por transacción
- **💳 Control de Créditos Integral**: Sistema completo de gestión crediticia con límites, pagos y morosidad
- **📊 Dashboard con Widgets**: Múltiples widgets interactivos con métricas en tiempo real
- **🤖 Metas Inteligentes**: Cálculo automático de objetivos basado en datos históricos y tendencias
- **💰 Soporte para Pesos Mexicanos**: Formato de moneda MXN con locale mexicano
- **⏰ Control de Vencimientos**: Seguimiento de fechas de vencimiento y alertas
- **🏢 Gestión Completa**: Clientes, proveedores, productos, compras, ventas y gastos
- **👥 Sistema de Roles**: Control de acceso granular por rol de usuario
- **🎨 Panel Administrativo**: Interfaz moderna con Filament 3
- **📈 Reportes Visuales**: Análisis gráfico de ventas, stock, créditos y cobranza

## 🛠️ Tecnologías Utilizadas

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend Admin**: Filament 3 con Widgets, Charts y Repeaters
- **Base de Datos**: SQLite con migraciones automáticas
- **Autenticación**: Laravel Sanctum con roles
- **UI Components**: Blade + Livewire + Chart.js
- **Localización**: Español México (es_MX)
- **Moneda**: Peso Mexicano (MXN)

## 🛒 Sistema de Compras Multi-Producto

### 🔄 Nueva Arquitectura de Compras
- **Modelo Compra**: Información general de la transacción (proveedor, fecha, total, tipo de pago)
- **Modelo CompraItem**: Items individuales por compra (producto, cantidad, precio, lote, vencimiento)
- **Relación hasMany**: Una compra puede tener múltiples productos
- **Estructura Normalizada**: Eliminación de redundancia y mejor organización de datos

### 🎨 Interfaz con Repeater
- **Múltiples Productos**: Agregar/eliminar productos dinámicamente sin perder datos
- **Cálculos Automáticos**: Subtotal por item y total general en tiempo real
- **Validaciones**: Mínimo 1 producto requerido por compra
- **Etiquetas Dinámicas**: Cada item muestra producto y subtotal
- **Reordenamiento**: Items reorganizables con botones
- **Confirmaciones**: Protección al eliminar items

### 💰 Tipos de Pago Avanzados
- **Contado**: Pago inmediato al recibir mercancía
- **Crédito**: Pago diferido con fecha límite
- **Crédito + Enganche**: Pago parcial inicial más saldo a crédito
- **Generación Automática**: Pagos de enganche creados automáticamente

### 📦 Gestión de Lotes PEPS
- **Lotes Automáticos**: Generación por item con formato LOTE-001-2025
- **Numeración Inteligente**: Reinicio automático por año
- **Fechas de Vencimiento**: Control individual por producto
- **Integración Inventario**: Creación automática de registros PEPS al recibir

### 🔔 Notificaciones Inteligentes
- **Diferencias de Precio**: Alertas cuando el precio difiere >10% del promedio histórico
- **Comparación por Producto**: Análisis individual de cada item
- **Persistencia**: Notificaciones importantes permanecen visibles
- **Contexto Detallado**: Información específica del producto y porcentaje de diferencia

## 📊 Dashboard y Widgets

### 🍎 Módulo de Productos
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

### 🏢 Módulo de Proveedores
1. **EstadisticasProveedoresWidget** - Métricas Generales
   - Total de proveedores activos
   - Límites de crédito promedio
   - Proveedores con saldo pendiente
   - Estado del crédito por proveedor

2. **ProveedoresMayorDeudaWidget** - Control de Deudas
   - Top proveedores con mayor saldo pendiente
   - Información de contacto y documentos
   - Estado de crédito con indicadores visuales
   - Formato de moneda mexicana

3. **EvolucionPagosChart** - Análisis Temporal Inteligente
   - Evolución de pagos a proveedores (6 meses)
   - **🤖 Sistema de Metas Automáticas**: Cálculo inteligente basado en:
     - Promedio trimestral de pagos históricos
     - Saldos pendientes por proveedor
     - Factores estacionales del negocio
     - Tendencias de mejora y crecimiento
   - Comparación automática: pagos realizados vs metas inteligentes
   - Gráfico de líneas con formato MXN
   - Seguimiento predictivo de flujo de caja

## 🏢 Gestión de Proveedores y Pagos

### 📋 Sistema de Proveedores
- **Gestión Completa**: CRUD de proveedores con validaciones
- **Control de Crédito**: Límites crediticios y días de pago
- **RFC**: Campo único de 13 caracteres alfanuméricos (formato mexicano)
- **Estados**: Activo/Inactivo con control de acceso
- **Información de Contacto**: Teléfono, email, dirección
- **Descuentos Especiales**: Porcentajes personalizados

### 💳 Gestión de Pagos a Proveedores
- **Tipos de Pago**: Pago, anticipo, abono
- **Métodos de Pago**: Efectivo, transferencia, cheque, tarjeta
- **Trazabilidad Completa**: Usuario, fecha, referencia
- **Control de Saldos**: Actualización automática en tiempo real
- **Historial Detallado**: Registro de todos los movimientos
- **Formato de Moneda**: Pesos mexicanos (MXN)

## 💳 Gestión de Créditos y Control Financiero

### 🛡️ Sistema de Créditos Integral
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

## 🔄 Sistema PEPS (Primero en Entrar, Primero en Salir)

### 📦 Control de Inventario
- **Entrada Automática**: Registros de inventario al recibir compras
- **Salida PEPS**: Consumo automático del stock más antiguo en ventas
- **Trazabilidad de Lotes**: Seguimiento completo por lote
- **Fechas de Vencimiento**: Control de productos próximos a vencer
- **Estados de Inventario**: Disponible, Reservado, Vencido

### 🏷️ Gestión de Lotes
- **Generación Automática**: Formato LOTE-001-2025
- **Numeración Inteligente**: Secuencial con reinicio anual
- **Información Completa**: Fecha de ingreso, vencimiento, proveedor
- **Búsqueda Avanzada**: Filtros por lote, fecha, estado

## 📈 Análisis y Reportes

### 📊 Widgets de Rendimiento
- **Actualización en Tiempo Real**: Datos actualizados automáticamente
- **Gráficos Interactivos**: Chart.js con tooltips informativos
- **Filtros Dinámicos**: Personalización de períodos y criterios
- **Exportación**: Datos descargables en diferentes formatos

### 🎯 Metas Inteligentes
- **Cálculo Automático**: Basado en datos históricos y tendencias
- **Factores Estacionales**: Consideración de variaciones temporales
- **Alertas Proactivas**: Notificaciones de desviaciones significativas
- **Seguimiento Continuo**: Monitoreo de cumplimiento de objetivos

## 🚀 Instalación y Configuración

### 📋 Requisitos
- PHP 8.2 o superior
- Composer 2.x
- Node.js 18+ y NPM
- SQLite 3.x

### ⚙️ Instalación

```bash
# Clonar el repositorio
git clone https://github.com/tu-usuario/frutiflow.git
cd frutiflow

# Instalar dependencias de PHP
composer install

# Instalar dependencias de Node.js
npm install

# Configurar el archivo de entorno
cp .env.example .env
php artisan key:generate

# Ejecutar migraciones y seeders
php artisan migrate --seed

# Compilar assets
npm run build

# Iniciar el servidor de desarrollo
php artisan serve
```

### 🗃️ Base de Datos
El sistema utiliza SQLite por defecto para facilitar el desarrollo y despliegue. La base de datos se crea automáticamente en `database/database.sqlite`.

### 👤 Usuario por Defecto
- **Email**: admin@frutiflow.com
- **Contraseña**: password
- **Rol**: Administrador

## 🔧 Funcionalidades Técnicas

### 🛡️ Seguridad
- **Autenticación**: Laravel Sanctum
- **Autorización**: Sistema de roles y permisos
- **Validaciones**: Reglas de validación robustas
- **Sanitización**: Limpieza automática de datos de entrada

### 🔄 Observers y Events
- **CompraObserver**: Automatización de procesos de compra
- **PagoClienteObserver**: Actualización automática de saldos
- **PagoProveedorObserver**: Control de pagos a proveedores
- **Notificaciones**: Sistema de alertas en tiempo real

### 🎨 UI/UX
- **Filament 3**: Panel administrativo moderno y responsivo
- **Widgets Personalizados**: Componentes específicos del negocio
- **Tema Personalizado**: Colores y diseño adaptado al sector
- **Navegación Intuitiva**: Menús organizados por módulos

## 🌟 Módulos del Sistema

### 👥 Clientes
- Gestión completa de clientes
- Control de crédito individual
- Historial de compras y pagos
- Estados de cuenta detallados

### 🏭 Proveedores
- Catálogo de proveedores activos
- Control de pagos y deudas
- Historial de compras realizadas
- Evaluación de desempeño

### 🍎 Productos
- Catálogo de frutas y productos
- Control de precios y márgenes
- Gestión de grupos y categorías
- Seguimiento de rentabilidad

### 📦 Inventario
- Control PEPS automatizado
- Alertas de stock mínimo
- Productos próximos a vencer
- Valorización de inventario

### 🛒 Compras
- **Nueva Arquitectura Multi-Producto**
- Registro con múltiples items
- Tipos de pago flexibles
- Generación automática de lotes
- Integración directa con inventario

### 💰 Ventas
- Procesamiento PEPS automático
- Control de crédito en tiempo real
- Múltiples métodos de pago
- Facturación integrada

### 🧾 Gastos
- Categorización de gastos operativos
- Control presupuestario
- Análisis de costos
- Reportes de rentabilidad

## 📞 Soporte y Contribución

### 🐛 Reportar Errores
Para reportar errores, por favor crea un issue en GitHub incluyendo:
- Descripción detallada del problema
- Pasos para reproducir el error
- Screenshots si es aplicable
- Información del entorno (PHP, Laravel, etc.)

### 🤝 Contribuir
Las contribuciones son bienvenidas. Por favor:
1. Fork el repositorio
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -am 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crea un Pull Request

### 📧 Contacto
- **Desarrollador**: [Tu Nombre]
- **Email**: tu-email@ejemplo.com
- **GitHub**: [tu-usuario](https://github.com/tu-usuario)

## 📄 Licencia

Este proyecto está licenciado bajo la Licencia MIT. Consulta el archivo `LICENSE` para más detalles.

---

*Desarrollado con ❤️ para la gestión eficiente de inventarios de frutas en México*
