<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Productos - {{ $user->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; color: #2d5537; margin-bottom: 30px; }
        .user-info { background: #e8f5e8; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .productos-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 15px; }
        .producto-card { background: #f9f9f9; padding: 15px; border-radius: 5px; border-left: 4px solid #4CAF50; position: relative; }
        .producto-card.selected { background: #e8f5e8; border-left-color: #2196F3; }
        .checkbox { position: absolute; top: 10px; right: 10px; }
        .btn { background: #4CAF50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; border: none; cursor: pointer; }
        .btn:hover { background: #45a049; }
        .btn.secondary { background: #6c757d; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛒 Asignar Productos</h1>
            <p>Usuario: <strong>{{ $user->name }}</strong></p>
        </div>

        <div class="user-info">
            <h3>👤 Información del Usuario</h3>
            <p><strong>Nombre:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Rol:</strong> {{ $user->role->nombre ?? 'Sin rol' }}</p>
        </div>

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('permisos.guardar-productos', $user) }}">
            @csrf
            
            <div style="margin-bottom: 20px;">
                <button type="button" onclick="selectAll()" class="btn">Seleccionar Todos</button>
                <button type="button" onclick="selectNone()" class="btn secondary">Deseleccionar Todos</button>
            </div>

            <div class="productos-grid">
                @foreach($productos as $producto)
                    <div class="producto-card {{ in_array($producto->id, $productosAsignados) ? 'selected' : '' }}" onclick="toggleProduct({{ $producto->id }})">
                        <input type="checkbox" 
                               name="productos[]" 
                               value="{{ $producto->id }}" 
                               id="producto_{{ $producto->id }}"
                               class="checkbox"
                               {{ in_array($producto->id, $productosAsignados) ? 'checked' : '' }}>
                        
                        <h4>{{ $producto->nombre }}</h4>
                        <p><strong>Código:</strong> {{ $producto->codigo }}</p>
                        <p><strong>Precio:</strong> ${{ number_format($producto->precio_venta, 2) }}</p>
                        <p><strong>Stock:</strong> {{ $producto->stock_actual }} {{ $producto->unidad_medida }}</p>
                        <p><strong>Estado:</strong> {{ $producto->activo ? '✅ Activo' : '❌ Inactivo' }}</p>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 30px; text-align: center;">
                <button type="submit" class="btn">💾 Guardar Asignación</button>
                <a href="{{ route('permisos.dashboard') }}" class="btn secondary">❌ Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        function toggleProduct(id) {
            const checkbox = document.getElementById('producto_' + id);
            const card = checkbox.closest('.producto-card');
            
            checkbox.checked = !checkbox.checked;
            
            if (checkbox.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        }

        function selectAll() {
            const checkboxes = document.querySelectorAll('input[name="productos[]"]');
            const cards = document.querySelectorAll('.producto-card');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            
            cards.forEach(card => {
                card.classList.add('selected');
            });
        }

        function selectNone() {
            const checkboxes = document.querySelectorAll('input[name="productos[]"]');
            const cards = document.querySelectorAll('.producto-card');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            cards.forEach(card => {
                card.classList.remove('selected');
            });
        }
    </script>
</body>
</html>
