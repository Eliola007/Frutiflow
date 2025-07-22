<!-- Use this file to provide workspace-specific custom instructions to Copilot. For more details, visit https://code.visualstudio.com/docs/copilot/copilot-customization#_use-a-githubcopilotinstructionsmd-file -->

# Frutiflow - Sistema de Gestión de Inventario de Frutas

## Descripción del Proyecto
Frutiflow es un sistema de gestión de inventario de frutas desarrollado con Laravel 11 y Filament 3. Implementa la lógica PEPS (Primero en Entrar, Primero en Salir) para el manejo del inventario.

## Arquitectura del Proyecto

### Modelos Principales:
- **User**: Usuarios del sistema con roles específicos
- **Role**: Roles y permisos del sistema
- **Cliente**: Clientes que compran productos
- **Proveedor**: Proveedores que suministran productos
- **Producto**: Catálogo de frutas y productos
- **Compra**: Registro de compras a proveedores
- **Venta**: Registro de ventas a clientes
- **Gasto**: Registro de gastos operativos
- **Inventario**: Control de stock con lógica PEPS

### Lógica PEPS (FIFO)
El sistema implementa la lógica "Primero en Entrar, Primero en Salir" a través del modelo Inventario:
- Cada compra genera registros de inventario con fechas de ingreso y vencimiento
- Las ventas consumen el inventario más antiguo primero
- Se controlan productos próximos a vencer y vencidos

### Características Técnicas:
- Laravel 11 con PHP 8.2+
- Filament 3 para administración
- Base de datos SQLite
- Autenticación y autorización por roles
- Validaciones y relaciones Eloquent
- Control de stock automatizado

### Estructura de Carpetas:
```
app/
├── Models/           # Modelos Eloquent
├── Filament/         # Recursos de Filament
└── Http/Controllers/ # Controladores

database/
├── migrations/       # Migraciones de BD
└── seeders/         # Datos iniciales
```

### Convenciones de Código:
- Nombres de modelos en singular y PascalCase
- Nombres de tablas en plural y snake_case
- Relaciones Eloquent claramente definidas
- Scopes para consultas complejas
- Uso de accessors y mutators para formateo

### Filament Resources:
Cada modelo tiene su recurso de Filament con:
- Formularios de creación/edición
- Tablas con filtros y búsqueda
- Validaciones específicas
- Acciones personalizadas

## Instrucciones Específicas para Copilot:
1. Mantén la consistencia con los modelos existentes
2. Respeta la lógica PEPS en todas las operaciones de inventario
3. Usa las relaciones Eloquent definidas
4. Implementa validaciones apropiadas
5. Sigue las convenciones de nomenclatura de Laravel
6. Utiliza los recursos de Filament para la administración
7. Considera las fechas de vencimiento en operaciones de inventario
