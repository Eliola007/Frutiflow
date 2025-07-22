<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Widgets de resumen -->
        @if ($this->hasHeaderWidgets())
            <div class="grid gap-6">
                <x-filament-widgets::widgets
                    :widgets="$this->getHeaderWidgets()"
                    :columns="$this->getHeaderWidgetsColumns()"
                />
            </div>
        @endif

        <!-- Gráficos principales -->
        <div class="grid gap-6 md:grid-cols-2">
            <x-filament-widgets::widgets
                :widgets="$this->getWidgets()"
                :columns="$this->getWidgetsColumns()"
            />
        </div>

        <!-- Información adicional -->
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div class="text-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Sistema de Gestión de Inventario Frutiflow
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Dashboard con métricas en tiempo real de tu inventario de productos frutícolas.
                    Los datos se actualizan automáticamente para reflejar el estado actual del stock,
                    precios y distribución por grupos.
                </p>
                <div class="mt-4 flex justify-center space-x-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">PEPS</div>
                        <div class="text-xs text-gray-500">Metodología</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">MXN</div>
                        <div class="text-xs text-gray-500">Moneda</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">Real-time</div>
                        <div class="text-xs text-gray-500">Actualización</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
