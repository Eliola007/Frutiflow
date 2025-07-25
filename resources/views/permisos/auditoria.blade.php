<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoría - Frutiflow</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; color: #2d5537; margin-bottom: 30px; }
        .audit-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .audit-table th, .audit-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .audit-table th { background-color: #f2f2f2; font-weight: bold; }
        .audit-table tr:hover { background-color: #f5f5f5; }
        .event-type { padding: 3px 8px; border-radius: 3px; color: white; font-size: 12px; }
        .event-created { background: #4CAF50; }
        .event-updated { background: #ff9800; }
        .event-deleted { background: #f44336; }
        .btn { background: #4CAF50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px; }
        .btn:hover { background: #45a049; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Registro de Auditoría</h1>
            <p>Últimas 50 actividades del sistema</p>
        </div>

        <table class="audit-table">
            <thead>
                <tr>
                    <th>Fecha/Hora</th>
                    <th>Usuario</th>
                    <th>Evento</th>
                    <th>Modelo</th>
                    <th>Registro ID</th>
                    <th>IP</th>
                    <th>Navegador</th>
                </tr>
            </thead>
            <tbody>
                @forelse($audits as $audit)
                <tr>
                    <td>{{ $audit->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $audit->user ? $audit->user->name : 'Sistema' }}</td>
                    <td>
                        <span class="event-type event-{{ $audit->event }}">
                            {{ ucfirst($audit->event) }}
                        </span>
                    </td>
                    <td>{{ class_basename($audit->auditable_type) }}</td>
                    <td>{{ $audit->auditable_id }}</td>
                    <td>{{ $audit->ip_address }}</td>
                    <td title="{{ $audit->user_agent }}">
                        {{ \Illuminate\Support\Str::limit($audit->user_agent, 50) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #666;">
                        No hay registros de auditoría
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 30px; text-align: center;">
            <a href="{{ route('permisos.dashboard') }}" class="btn">🏠 Volver al Dashboard</a>
        </div>
    </div>
</body>
</html>
