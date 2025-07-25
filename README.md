# 🍎 Frutiflow - Sistema de Gestión de Inventario de Frutas

Sistema completo de gestión de inventario de frutas desarrollado con Laravel 11 y Filament 3, implementando la lógica PEPS (Primero en Entrar, Primero en Salir) para el control de stock, con sistema integral de control de créditos, gestión de compras multi-producto y dashboard con gráficos en tiempo real.

## ✨ Características Principales

- **🔄 Gestión de Inventario PEPS**: Control automático de stock con lógica "Primero en Entrar, Primero en Salir"
- **🛒 Sistema de Compras Avanzado**: Compras multi-producto con Repeater para múltiples items por transacción
- **�️ Sistema de Ventas Integral**: Ventas multi-producto con control de inventario PEPS y gestión de crédito
- **�💳 Control de Créditos Integral**: Sistema completo de gestión crediticia con límites, pagos y morosidad
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

## �️ Sistema de Ventas Multi-Producto

### 🎯 Nueva Arquitectura de Ventas
- **Modelo Venta**: Información general de la transacción (cliente, fecha, total, método de pago, estado)
- **Modelo VentaItem**: Items individuales por venta (producto, cantidad, precio, descuento)
- **Relación hasMany**: Una venta puede incluir múltiples productos diferentes
- **Sistema MOSTRADOR**: Cliente predeterminado para ventas rápidas al contado
- **Numeración Automática**: Formato VEN-####-YYYY con secuencia anual

### 🎨 Interfaz Táctil Optimizada
- **Diseño Touch-Friendly**: Botones grandes y campos amplios para pantallas táctiles
- **Múltiples Productos**: Repeater dinámico para agregar/eliminar productos sin perder datos
- **Cálculos en Tiempo Real**: Subtotales, descuentos y total se actualizan automáticamente
- **Validación de Stock**: Verificación automática de disponibilidad antes de vender
- **Precio Automático**: Al seleccionar producto, se carga automáticamente el precio de venta
- **Cliente Predeterminado**: MOSTRADOR preseleccionado para ventas rápidas

### 💰 Gestión de Pagos y Crédito
- **Métodos de Pago**: Efectivo, tarjetas (débito/crédito), transferencia, crédito, mixto
- **Tipos de Venta**: Al contado o a crédito con validación de límites
- **Crédito Disponible**: Visualización en tiempo real del crédito del cliente
- **Monto Recibido y Cambio**: Cálculo automático para pagos en efectivo
- **Control de Crédito**: Validación automática de límites crediticios

### 🔄 Integración PEPS Automática
- **Deducción PEPS**: Al procesar venta, se deduce automáticamente del inventario más antiguo
- **Verificación de Stock**: Control previo de disponibilidad por producto
- **Actualización Instantánea**: Stock de productos se actualiza en tiempo real
- **Trazabilidad**: Registro de qué lotes se consumieron en cada venta
- **Estados Inteligentes**: Venta procesada = inventario actualizado automáticamente

### 🧮 Sistema de Descuentos Inteligente
- **Descuento por Unidad**: Descuento individual que se multiplica por la cantidad
- **Cálculo Automático**: (Cantidad × Precio) - (Descuento × Cantidad) = Subtotal
- **Descuento General**: Descuento adicional aplicable al total de la venta
- **Validación de Precios**: Campos de texto con validación numérica para mejor UX

### 📊 Estados y Control
- **Estados de Venta**: Pendiente, Procesada (predeterminado), Enviada, Entregada, Cancelada
- **Validaciones de Eliminación**: Solo ventas pendientes pueden eliminarse
- **Protección de Datos**: Confirmaciones antes de acciones críticas
- **Bulk Actions**: Operaciones masivas con validaciones de estado

## �📊 Dashboard y Widgets

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

### 🛍️ Módulo de Ventas
1. **VentasWidget** - Estadísticas de Ventas
   - Ventas de hoy vs ayer (comparación y tendencia)
   - Ventas del mes vs mes anterior
   - Ventas del año vs año anterior
   - Indicadores de crecimiento con colores dinámicos
   - Formato de moneda mexicana (MXN)
   - Actualización en tiempo real cada 30 segundos

2. **VentasEvolucionChart** - Análisis Temporal
   - Evolución de ventas de los últimos 12 meses
   - Gráfico de líneas con tendencias mensuales
   - Comparación año actual vs año anterior
   - Tooltips con información detallada

3. **VentasTopProductosChart** - Productos Más Vendidos
   - Top 10 productos por volumen de ventas
   - Gráfico de barras horizontales
   - Datos de cantidad vendida por producto
   - Colores diferenciados por rendimiento

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

### 👤 Usuarios por Defecto

#### 🔧 **Administrador Principal**
- **Email**: admin@frutiflow.com
- **Contraseña**: password
- **Rol**: Administrador
- **Permisos**: Acceso completo al sistema

#### 💰 **Usuario Cajero**
- **Email**: cajero@frutiflow.com
- **Contraseña**: password
- **Rol**: Cajero
- **Permisos**: Ventas, clientes, productos (consulta), pagos de clientes

#### 🤝 **Usuario Socio Comercial**
- **Email**: socio@frutiflow.com
- **Contraseña**: password
- **Rol**: Socio Comercial
- **Permisos**: Productos asignados únicamente, ventas restringidas

#### 📊 **Usuario Reporteador**
- **Email**: reportes@frutiflow.com
- **Contraseña**: password
- **Rol**: Reporteador
- **Permisos**: Solo lectura, acceso completo a reportes

#### 👀 **Usuario Visualizador**
- **Email**: visualizador@frutiflow.com
- **Contraseña**: password
- **Rol**: Visualizador
- **Permisos**: Consulta básica sin datos financieros

#### 🔒 **Usuario Invitado**
- **Email**: invitado@frutiflow.com
- **Contraseña**: password
- **Rol**: Invitado
- **Permisos**: Acceso mínimo para demostraciones

**Nota**: Cambia todas las contraseñas en producción por seguridad.

## 🔧 Funcionalidades Técnicas

### 🛡️ Seguridad
- **Autenticación**: Sistema de login personalizado con Filament
- **Autorización**: Sistema de roles y permisos granular con Spatie Permission
- **Validaciones**: Reglas de validación robustas en todos los formularios
- **Sanitización**: Limpieza automática de datos de entrada
- **Auditoría**: Trazabilidad completa con Laravel Auditing (Owen-It)
- **Middleware Personalizado**: Control de acceso por permisos y productos

### 🔄 Observers y Events
- **CompraObserver**: Automatización de procesos de compra e inventario
- **PagoClienteObserver**: Actualización automática de saldos de clientes
- **PagoProveedorObserver**: Control automático de pagos a proveedores
- **VentaObserver**: Procesamiento PEPS automático en ventas
- **UserObserver**: Control de cambios en usuarios y roles
- **Auditing Events**: Registro automático de todas las operaciones
- **Notificaciones**: Sistema de alertas en tiempo real y por email

### 🎨 UI/UX
- **Filament 3**: Panel administrativo moderno y responsivo
- **Widgets Personalizados**: Componentes específicos del negocio
- **Interfaz Táctil**: Optimizada para tablets y pantallas touch
- **Repeaters Dinámicos**: Formularios multi-item sin perder datos
- **Validación en Tiempo Real**: Feedback inmediato al usuario
- **Tema Personalizado**: Colores y diseño adaptado al sector
- **Navegación Intuitiva**: Menús organizados por módulos

## 🔐 Sistema de Roles y Permisos

### 🛡️ Arquitectura de Seguridad
El sistema implementa un control de acceso granular basado en **Spatie Permission** con auditoría completa utilizando **Laravel Auditing (Owen-It)**. Esto garantiza tanto la seguridad como la trazabilidad total de todas las operaciones.

### 👥 Roles del Sistema

#### 🔧 **Administrador**
- **Acceso Completo**: Todas las funcionalidades del sistema
- **Gestión de Usuarios**: Crear, editar y asignar roles
- **Configuración**: Parámetros generales y configuraciones avanzadas
- **Reportes Avanzados**: Acceso a todos los reportes y análisis
- **Auditoría**: Visualización completa del log de auditoría

#### 💰 **Cajero**
- **Ventas**: Crear, procesar y gestionar ventas
- **Clientes**: Ver y gestionar información de clientes
- **Productos**: Consultar catálogo y precios
- **Inventario**: Ver stock disponible
- **Pagos de Clientes**: Registrar cobros y abonos
- **Sin Acceso**: Compras, configuración, reportes financieros

#### 🤝 **Socio Comercial**
- **Acceso Limitado por Productos**: Solo productos asignados específicamente
- **Ventas Restringidas**: Solo productos de su cartera
- **Clientes Propios**: Gestión de sus clientes asignados
- **Inventario Filtrado**: Solo stock de productos asignados
- **Reportes Básicos**: Métricas de sus productos únicamente

#### 📊 **Reporteador**
- **Solo Lectura**: Acceso de consulta a todos los módulos
- **Reportes Completos**: Generar y exportar todos los reportes
- **Dashboard**: Visualizar todos los widgets y métricas
- **Sin Modificaciones**: No puede crear, editar o eliminar registros

#### 👀 **Visualizador**
- **Consulta Básica**: Ver información general del sistema
- **Dashboard Limitado**: Widgets básicos sin datos sensibles
- **Sin Reportes**: No acceso a reportes detallados
- **Sin Datos Financieros**: No ver precios, costos o márgenes

#### 🔒 **Invitado**
- **Acceso Mínimo**: Solo dashboard básico
- **Demo**: Perfecto para demostraciones del sistema
- **Sin Datos Reales**: No acceso a información comercial

### 📋 Sistema de Permisos Granulares

El sistema cuenta con **52 permisos específicos** organizados por módulos:

#### 🍎 **Productos** (6 permisos)
- `productos.ver` - Visualizar catálogo de productos
- `productos.crear` - Crear nuevos productos
- `productos.editar` - Modificar productos existentes
- `productos.eliminar` - Eliminar productos
- `productos.importar` - Importar catálogos masivos
- `productos.exportar` - Exportar listados de productos

#### 💳 **Ventas** (6 permisos)
- `ventas.ver` - Consultar ventas realizadas
- `ventas.crear` - Procesar nuevas ventas
- `ventas.editar` - Modificar ventas pendientes
- `ventas.eliminar` - Cancelar ventas (solo pendientes)
- `ventas.reportes` - Generar reportes de ventas
- `ventas.dashboard` - Ver métricas en dashboard

#### 🛒 **Compras** (6 permisos)
- `compras.ver` - Consultar historial de compras
- `compras.crear` - Registrar nuevas compras
- `compras.editar` - Modificar compras pendientes
- `compras.eliminar` - Eliminar compras
- `compras.reportes` - Reportes de compras
- `compras.recibir` - Procesar recepción de mercancía

#### 📦 **Inventario** (5 permisos)
- `inventario.ver` - Consultar stock disponible
- `inventario.ajustar` - Realizar ajustes de inventario
- `inventario.transferir` - Transferencias entre ubicaciones
- `inventario.reportes` - Reportes de inventario
- `inventario.alertas` - Ver alertas de stock y vencimientos

#### 👥 **Clientes** (5 permisos)
- `clientes.ver` - Consultar base de clientes
- `clientes.crear` - Registrar nuevos clientes
- `clientes.editar` - Modificar información de clientes
- `clientes.eliminar` - Eliminar clientes
- `clientes.credito` - Gestionar límites de crédito

#### 🏭 **Proveedores** (5 permisos)
- `proveedores.ver` - Consultar catálogo de proveedores
- `proveedores.crear` - Registrar nuevos proveedores
- `proveedores.editar` - Modificar información
- `proveedores.eliminar` - Eliminar proveedores
- `proveedores.pagos` - Gestionar pagos a proveedores

#### 📊 **Reportes** (4 permisos)
- `reportes.ventas` - Reportes de ventas y clientes
- `reportes.compras` - Reportes de compras y proveedores
- `reportes.inventario` - Reportes de stock y movimientos
- `reportes.financieros` - Reportes financieros y rentabilidad

#### ⚙️ **Configuración** (4 permisos)
- `configuracion.general` - Parámetros generales del sistema
- `configuracion.usuarios` - Gestionar usuarios y roles
- `configuracion.sistema` - Configuraciones avanzadas
- `configuracion.backup` - Respaldos y restauración

#### 👤 **Usuarios** (4 permisos)
- `usuarios.ver` - Consultar lista de usuarios
- `usuarios.crear` - Crear nuevos usuarios
- `usuarios.editar` - Modificar usuarios existentes
- `usuarios.roles` - Asignar y modificar roles

#### 💰 **Gastos** (4 permisos)
- `gastos.ver` - Consultar registro de gastos
- `gastos.crear` - Registrar nuevos gastos
- `gastos.editar` - Modificar gastos existentes
- `gastos.eliminar` - Eliminar gastos

#### 💳 **Pagos** (3 permisos)
- `pagos.clientes` - Gestionar cobros de clientes
- `pagos.proveedores` - Gestionar pagos a proveedores
- `pagos.reportes` - Reportes de flujo de efectivo

### 🔍 Sistema de Auditoría Completa

#### 📝 **Registro Automático**
- **Todas las Operaciones**: Cada acción queda registrada automáticamente
- **Información Detallada**: Usuario, IP, navegador, fecha/hora exacta
- **Cambios de Valores**: Before/After de cada campo modificado
- **Eventos del Sistema**: Login, logout, cambios de roles, etc.

#### 🏢 **Panel de Administración**
- **Recurso de Auditoría**: Interfaz completa en Filament para revisar logs
- **Filtros Avanzados**: Por usuario, fecha, tipo de evento, modelo
- **Búsqueda Inteligente**: Localizar eventos específicos rápidamente
- **Exportación**: Generar reportes de auditoría para compliance

#### 📧 **Notificaciones de Seguridad**
- **Cambios Críticos**: Notificaciones automáticas por email
- **Intentos de Acceso**: Alertas de accesos no autorizados
- **Modificaciones de Roles**: Notificación inmediata a administradores
- **Configuración Flexible**: Personalizar qué eventos notificar

### 🎯 Sistema de Asignación de Productos

#### 📋 **Para Socios Comerciales**
- **Productos Específicos**: Asignación individual por socio comercial
- **Fechas de Vigencia**: Control temporal de asignaciones
- **Estado Activo/Inactivo**: Activar/desactivar productos dinámicamente
- **Interfaz Táctil**: Sistema de selección múltiple fácil de usar

#### 🔒 **Control de Acceso**
- **Filtrado Automático**: Solo ven productos asignados
- **Validaciones**: No pueden vender productos no asignados
- **Middleware Personalizado**: Verificación en cada operación
- **Reportes Filtrados**: Métricas solo de productos permitidos

### 🧪 **Sistema de Pruebas**

#### 🔍 **Panel de Testing**
- **Dashboard de Permisos**: Interfaz para probar funcionalidades por rol
- **Simulación de Usuarios**: Probar el sistema desde perspectiva de cada rol
- **Validación de Accesos**: Verificar que las restricciones funcionen correctamente
- **Testing de Productos**: Probar asignaciones de socios comerciales

#### 📊 **Métricas de Permisos**
- **Contadores en Tiempo Real**: Cuántos permisos tiene cada rol
- **Usuarios por Rol**: Distribución de usuarios en el sistema
- **Productos Asignados**: Control de asignaciones por socio
- **Log de Auditoría**: Últimas actividades del sistema

### 🚀 **Implementación Técnica**

#### 📦 **Tecnologías Utilizadas**
- **Spatie Permission**: Gestión robusta de roles y permisos
- **Laravel Auditing (Owen-It)**: Sistema de auditoría automática
- **Filament Resources**: Interfaces administrativas completas
- **Middleware Personalizado**: Control de acceso granular
- **Observers**: Automatización de procesos de auditoría

#### 🛠️ **Características Avanzadas**
- **Seeder Inteligente**: Configuración automática de permisos y roles
- **Migraciones Seguras**: Evita conflictos con tablas existentes
- **Cache Optimizado**: Rendimiento mejorado en verificación de permisos
- **Validaciones Robustas**: Verificaciones múltiples antes de acciones críticas

### 🚀 Mejoras Recientes (v2.0)
- **Sistema de Ventas Multi-Producto**: Nueva arquitectura con VentaItem
- **Interfaz Táctil Mejorada**: Botones grandes y campos optimizados
- **Validación de Campos Numéricos**: Conversión segura de texto a número
- **Descuentos Inteligentes**: Sistema de descuentos por unidad
- **Widget de Ventas**: Dashboard actualizado con métricas de ventas
- **Cliente MOSTRADOR**: Sistema de ventas rápidas al contado
- **Estados de Venta**: Control de flujo con validaciones
- **Protección de Datos**: Validaciones antes de eliminar registros

### 🔐 Mejoras de Seguridad (v2.1)
- **Sistema de Roles y Permisos**: Implementación completa con Spatie Permission
- **6 Roles Específicos**: Administrador, Cajero, Socio Comercial, Reporteador, Visualizador, Invitado
- **52 Permisos Granulares**: Control detallado por módulo y funcionalidad
- **Auditoría Completa**: Sistema de trazabilidad con Laravel Auditing
- **Asignación de Productos**: Control específico para socios comerciales
- **Panel de Administración**: Recursos Filament para gestión completa
- **Sistema de Testing**: Interface para validar permisos y funcionalidades
- **Middleware Personalizado**: Verificaciones de acceso en tiempo real
- **Notificaciones de Seguridad**: Alertas automáticas por email
- **Usuarios de Demo**: 6 usuarios preconfigurados para testing

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

### �️ Ventas
- **Nueva Arquitectura Multi-Producto**
- Sistema de ventas con múltiples items por transacción
- Interfaz táctil optimizada para point-of-sale
- Integración PEPS automática con inventario
- Control de crédito en tiempo real
- Múltiples métodos de pago (efectivo, tarjetas, transferencia, crédito)
- Cliente MOSTRADOR para ventas rápidas
- Descuentos inteligentes por unidad y generales
- Numeración automática (VEN-####-YYYY)
- Validación de stock en tiempo real
- Estados de venta con control de flujo
- Dashboard con widgets de análisis de ventas

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
