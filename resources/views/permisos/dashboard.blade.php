<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frutiflow - Test de Permisos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; color: #2d5537; margin-bottom: 30px; }
        .user-info { background: #e8f5e8; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .section { margin-bottom: 30px; }
        .section h3 { color: #2d5537; border-bottom: 2px solid #4CAF50; padding-bottom: 5px; }
        .badge { background: #4CAF50; color: white; padding: 3px 8px; border-radius: 3px; font-size: 12px; margin: 2px; }
        .badge.role { background: #ff9800; }
        .badge.permission { background: #2196F3; }
        .btn { background: #4CAF50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; }
        .btn:hover { background: #45a049; }
        .btn.danger { background: #f44336; }
        .productos-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 15px; }
        .producto-card { background: #f9f9f9; padding: 15px; border-radius: 5px; border-left: 4px solid #4CAF50; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🍎 Frutiflow - Panel de Permisos</h1>
            <p>Sistema de Roles y Permisos con Auditoría</p>
        </div>

        <div class="user-info">
            <h3>👤 Información del Usuario</h3>
            <p><strong>Nombre:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Rol tradicional:</strong> {{ $user->role->nombre ?? 'Sin rol' }}</p>
        </div>

        <div class="section">
            <h3>🎭 Roles de Spatie</h3>
            @forelse($roles as $role)
                <span class="badge role">{{ $role }}</span>
            @empty
                <p>Sin roles asignados</p>
            @endforelse
        </div>

        <div class="section">
            <h3>🔐 Permisos</h3>
            @forelse($permissions as $permission)
                <span class="badge permission">{{ $permission }}</span>
            @empty
                <p>Sin permisos asignados</p>
            @endforelse
        </div>

        @if($user->hasRole('Socio Comercial'))
        <div class="section">
            <h3>🏷️ Productos Asignados (Socio Comercial)</h3>
            @if($productosAsignados->count() > 0)
                <div class="productos-grid">
                    @foreach($productosAsignados as $producto)
                        <div class="producto-card">
                            <h4>{{ $producto->nombre }}</h4>
                            <p><strong>Código:</strong> {{ $producto->codigo }}</p>
                            <p><strong>Asignado:</strong> {{ $producto->pivot->asignado_en->format('d/m/Y') }}</p>
                            <p><strong>Hasta:</strong> {{ $producto->pivot->asignado_hasta->format('d/m/Y') }}</p>
                            <p><strong>Estado:</strong> {{ $producto->pivot->activo ? '✅ Activo' : '❌ Inactivo' }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p>No tienes productos asignados</p>
            @endif
        </div>
        @endif

        <div class="section">
            <h3>🧪 Pruebas de Permisos</h3>
            <div>
                <a href="{{ route('permisos.productos') }}" class="btn">Ver Productos</a>
                <a href="{{ route('permisos.ventas') }}" class="btn">Ver Ventas</a>
                <a href="{{ route('permisos.auditoria') }}" class="btn">Ver Auditoría</a>
                
                @if($user->can('productos.crear'))
                    <form style="display: inline-block;" method="POST" action="{{ route('permisos.crear-producto') }}">
                        @csrf
                        <button type="submit" class="btn">Crear Producto de Prueba</button>
                    </form>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-top: 20px;">
                ✅ {{ session('success') }}
            </div>
        @endif

        <div style="margin-top: 30px; text-align: center; padding-top: 20px; border-top: 1px solid #ddd;">
            <a href="/admin" class="btn">🏠 Volver al Admin</a>
            <form style="display: inline-block;" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn danger">🚪 Cerrar Sesión</button>
            </form>
        </div>
    </div>
</body>
</html>
