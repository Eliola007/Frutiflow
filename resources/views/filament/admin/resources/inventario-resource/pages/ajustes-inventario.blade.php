<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header con información -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        Ajustes de Inventario
                    </h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Realiza correcciones de stock por diferencias físicas, mermas o errores de registro. 
                        Todos los ajustes quedan registrados en el log del sistema.
                    </p>
                </div>
            </div>
        </div>

        <!-- Información importante -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        Información Importante
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>Los ajustes son permanentes</strong> y no se pueden deshacer fácilmente</li>
                            <li>Siempre verifica el conteo físico antes de realizar ajustes</li>
                            <li>Documenta claramente el motivo del ajuste</li>
                            <li>Solo ajusta lotes con stock disponible (no vencidos)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-lg shadow">
            <form wire:submit="procesar_ajuste">
                {{ $this->form }}
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                    <div class="flex justify-end space-x-3">
                        @foreach($this->getFormActions() as $action)
                            {{ $action }}
                        @endforeach
                    </div>
                </div>
            </form>
        </div>

        <!-- Historial reciente (si quieres agregar esto después) -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                📋 Guía Rápida de Ajustes
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                    <h4 class="font-medium text-green-800 mb-2">➕ Incremento</h4>
                    <p class="text-sm text-green-700">
                        Cuando encuentras más stock del registrado en el sistema. 
                        Ejemplo: Error en registro anterior, producto encontrado.
                    </p>
                </div>
                
                <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                    <h4 class="font-medium text-red-800 mb-2">➖ Decremento</h4>
                    <p class="text-sm text-red-700">
                        Cuando hay menos stock del registrado. 
                        Ejemplo: Merma, robo, daño, vencimiento no detectado.
                    </p>
                </div>
                
                <div class="border border-blue-200 rounded-lg p-4 bg-blue-50">
                    <h4 class="font-medium text-blue-800 mb-2">🔄 Corrección</h4>
                    <p class="text-sm text-blue-700">
                        Ajuste general del inventario basado en conteo físico. 
                        Usado en inventarios periódicos.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
