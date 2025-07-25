<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Prueba de Permisos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; color: #2d5537; margin-bottom: 30px; }
        .productos-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; margin-top: 15px; }
        .producto-card { background: #f9f9f9; padding: 15px; border-radius: 5px; border-left: 4px solid #4CAF50; }
        .btn { background: #4CAF50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; }
        .btn:hover { background: #45a049; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛒 Lista de Productos</h1>
            <p>Usuario: <strong>{{ $user->name }}</strong> | Permisos verificados ✅</p>
        </div>

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <div class="productos-grid">
            @foreach($productos as $producto)
                <div class="producto-card">
                    <h4>{{ $producto->nombre }}</h4>
                    <p><strong>Código:</strong> {{ $producto->codigo }}</p>
                    <p><strong>Precio:</strong> ${{ number_format($producto->precio_venta, 2) }}</p>
                    <p><strong>Stock:</strong> {{ $producto->stock_actual }} {{ $producto->unidad_medida }}</p>
                    <p><strong>Estado:</strong> {{ $producto->activo ? '✅ Activo' : '❌ Inactivo' }}</p>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 30px; text-align: center;">
            <a href="{{ route('permisos.dashboard') }}" class="btn">🏠 Volver al Dashboard</a>
        </div>
    </div>
</body>
</html>
