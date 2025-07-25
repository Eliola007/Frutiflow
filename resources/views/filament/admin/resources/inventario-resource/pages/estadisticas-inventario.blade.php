<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header con información general -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">
                        📊 Panel de Control PEPS
                    </h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Análisis integral del inventario con metodología Primera Entrada, Primera Salida
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('inventario.reporte-general') }}" 
                       target="_blank"
                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        📄 Reporte General
                    </a>
                    <a href="{{ route('inventario.exportar-csv') }}" 
                       target="_blank"
                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        📥 Exportar CSV
                    </a>
                </div>
            </div>
        </div>

        <!-- Widgets de estadísticas -->
        <div class="grid grid-cols-1 gap-6">
            @foreach($this->getHeaderWidgets() as $widget)
                @livewire($widget)
            @endforeach
        </div>

        <!-- Tabla de alertas y acciones rápidas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Alertas de vencimiento -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        ⚠️ Alertas de Vencimiento
                    </h3>
                    
                    @php
                        $alertasVencimiento = \App\Models\Inventario::where('cantidad_actual', '>', 0)
                            ->where('fecha_vencimiento', '>', now())
                            ->where('fecha_vencimiento', '<=', now()->addDays(15))
                            ->with('producto')
                            ->orderBy('fecha_vencimiento')
                            ->limit(5)
                            ->get();
                    @endphp

                    @if($alertasVencimiento->count() > 0)
                        <div class="space-y-3">
                            @foreach($alertasVencimiento as $alerta)
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $alerta->producto->nombre ?? 'Producto sin nombre' }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Lote: {{ $alerta->lote }} • 
                                            Stock: {{ $alerta->cantidad_actual }} • 
                                            Vence: {{ $alerta->fecha_vencimiento->format('d/m/Y') }}
                                        </p>
                                    </div>
                                    <div class="ml-4">
                                        @php
                                            $diasRestantes = now()->diffInDays($alerta->fecha_vencimiento, false);
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                     {{ $diasRestantes <= 7 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $diasRestantes }} días
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('inventario.reporte-vencimientos') }}" 
                               target="_blank"
                               class="text-sm text-indigo-600 hover:text-indigo-500">
                                Ver todas las alertas →
                            </a>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <p class="text-sm text-gray-500">
                                ✅ No hay productos próximos a vencer en los próximos 15 días
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Productos sin stock -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        📦 Productos Sin Stock
                    </h3>
                    
                    @php
                        $productosSinStock = \App\Models\Producto::whereDoesntHave('inventarios', function($query) {
                            $query->where('cantidad_actual', '>', 0)
                                  ->where('estado', '!=', 'vencido');
                        })->limit(5)->get();
                    @endphp

                    @if($productosSinStock->count() > 0)
                        <div class="space-y-3">
                            @foreach($productosSinStock as $producto)
                                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $producto->nombre }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Código: {{ $producto->codigo }}
                                        </p>
                                    </div>
                                    <div class="ml-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Sin stock
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4">
                            <a href="/admin/productos?tableFilters[sin_stock][value]=1" 
                               class="text-sm text-indigo-600 hover:text-indigo-500">
                                Ver todos los productos sin stock →
                            </a>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <p class="text-sm text-gray-500">
                                ✅ Todos los productos tienen stock disponible
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información sobre PEPS -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        Metodología PEPS (Primera Entrada, Primera Salida)
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>
                            Este sistema implementa el método PEPS para gestión de inventarios, garantizando que los productos más antiguos se vendan primero. 
                            Esto ayuda a minimizar pérdidas por vencimiento y mantener la frescura de los productos.
                        </p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Los lotes se ordenan automáticamente por fecha de ingreso</li>
                            <li>Las ventas consumen primero el stock más antiguo</li>
                            <li>Se generan alertas automáticas para productos próximos a vencer</li>
                            <li>El sistema calcula automáticamente el valor del inventario usando costos PEPS</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
