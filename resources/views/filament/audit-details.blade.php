<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <h4 class="font-semibold text-gray-900 dark:text-white">Información General</h4>
            <div class="mt-2 space-y-2">
                <div><strong>Usuario:</strong> {{ $record->user ? $record->user->name : 'Sistema' }}</div>
                <div><strong>Evento:</strong> 
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($record->event === 'created') bg-green-100 text-green-800
                        @elseif($record->event === 'updated') bg-yellow-100 text-yellow-800
                        @elseif($record->event === 'deleted') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($record->event) }}
                    </span>
                </div>
                <div><strong>Modelo:</strong> {{ class_basename($record->auditable_type) }}</div>
                <div><strong>ID Registro:</strong> {{ $record->auditable_id }}</div>
                <div><strong>IP:</strong> {{ $record->ip_address }}</div>
                <div><strong>Fecha:</strong> {{ $record->created_at->format('d/m/Y H:i:s') }}</div>
            </div>
        </div>
        
        <div>
            <h4 class="font-semibold text-gray-900 dark:text-white">Navegador</h4>
            <div class="mt-2">
                <div class="text-sm text-gray-600 dark:text-gray-400 break-all">
                    {{ $record->user_agent }}
                </div>
            </div>
        </div>
    </div>
    
    @if($record->old_values && count($record->old_values) > 0)
    <div>
        <h4 class="font-semibold text-gray-900 dark:text-white">Valores Anteriores</h4>
        <div class="mt-2 bg-red-50 dark:bg-red-900/20 p-3 rounded border">
            <pre class="text-sm text-red-700 dark:text-red-300 whitespace-pre-wrap">{{ json_encode($record->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>
    @endif
    
    @if($record->new_values && count($record->new_values) > 0)
    <div>
        <h4 class="font-semibold text-gray-900 dark:text-white">Valores Nuevos</h4>
        <div class="mt-2 bg-green-50 dark:bg-green-900/20 p-3 rounded border">
            <pre class="text-sm text-green-700 dark:text-green-300 whitespace-pre-wrap">{{ json_encode($record->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>
    @endif
    
    @if($record->url)
    <div>
        <h4 class="font-semibold text-gray-900 dark:text-white">URL</h4>
        <div class="mt-2">
            <div class="text-sm text-blue-600 dark:text-blue-400 break-all">
                {{ $record->url }}
            </div>
        </div>
    </div>
    @endif
</div>
