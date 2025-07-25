<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas - Prueba de Permisos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; color: #2d5537; margin-bottom: 30px; }
        .btn { background: #4CAF50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; }
        .btn:hover { background: #45a049; }
        .success { background: #d4edda; color: #155724; padding: 20px; border-radius: 5px; text-align: center; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💰 Módulo de Ventas</h1>
            <p>Acceso autorizado para tu rol</p>
        </div>

        <div class="success">
            <h3>✅ Acceso Concedido</h3>
            <p>Tienes permisos para ver el módulo de ventas.</p>
            <p>Aquí aparecería la interfaz completa de ventas con todas las funcionalidades permitidas para tu rol.</p>
        </div>

        <div style="margin-top: 30px; text-align: center;">
            <a href="{{ route('permisos.dashboard') }}" class="btn">🏠 Volver al Dashboard</a>
        </div>
    </div>
</body>
</html>
