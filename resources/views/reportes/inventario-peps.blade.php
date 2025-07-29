@extends('layouts.app')

@section('title', 'Reporte PEPS - ' . $producto->nombre)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-layer-group mr-2"></i>
                        Reporte PEPS - {{ $producto->nombre }}
                    </h3>
                    <div class="card-tools">
                        <span class="text-muted">
                            Generado el {{ now()->format('d/m/Y H:i:s') }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- Información del Producto -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Información del Producto</h5>
                                    <p><strong>Código:</strong> {{ $producto->codigo }}</p>
                                    <p><strong>Nombre:</strong> {{ $producto->nombre }}</p>
                                    <p><strong>Categoría:</strong> {{ $producto->categoria ?? 'Sin categoría' }}</p>
                                    <p><strong>Unidad:</strong> {{ $producto->unidad_medida }}</p>
                                    <p><strong>Stock Actual:</strong> {{ number_format($producto->stock_actual, 2) }} {{ $producto->unidad_medida }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Resumen PEPS</h5>
                                    <p><strong>Total en Inventario:</strong> {{ number_format($totalCantidad, 2) }} {{ $producto->unidad_medida }}</p>
                                    <p><strong>Valor Total:</strong> {{ \App\Helpers\CurrencyHelper::format($valorTotal) }}</p>
                                    <p><strong>Costo Promedio:</strong> {{ \App\Helpers\CurrencyHelper::format($costoPromedio) }}</p>
                                    <p><strong>Total de Lotes:</strong> {{ $inventarios->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Inventarios PEPS -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fas fa-list mr-2"></i>
                                Lotes en Inventario (Ordenados por PEPS)
                            </h4>
                            <p class="card-text text-muted">
                                Los lotes se muestran en el orden que serán consumidos según la lógica PEPS (Primero en Entrar, Primero en Salir)
                            </p>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Orden PEPS</th>
                                            <th>Lote</th>
                                            <th>Fecha Ingreso</th>
                                            <th>Fecha Vencimiento</th>
                                            <th>Cantidad Original</th>
                                            <th>Cantidad Actual</th>
                                            <th>Precio Costo</th>
                                            <th>Valor Total</th>
                                            <th>Estado</th>
                                            <th>Días Restantes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($inventarios as $index => $inventario)
                                            <tr class="
                                                @if($inventario->estado == 'vencido')
                                                    table-danger
                                                @elseif($inventario->fecha_vencimiento <= now()->addDays(3))
                                                    table-warning
                                                @elseif($inventario->fecha_vencimiento <= now()->addDays(7))
                                                    table-info
                                                @endif
                                            ">
                                                <td>
                                                    <span class="badge bg-primary">{{ $index + 1 }}</span>
                                                </td>
                                                <td>
                                                    <strong>{{ $inventario->lote }}</strong>
                                                    @if($inventario->numero_factura)
                                                        <br><small class="text-muted">Factura: {{ $inventario->numero_factura }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $inventario->fecha_ingreso->format('d/m/Y') }}</td>
                                                <td>
                                                    {{ $inventario->fecha_vencimiento->format('d/m/Y') }}
                                                    @if($inventario->fecha_vencimiento <= now())
                                                        <br><small class="text-danger"><strong>VENCIDO</strong></small>
                                                    @elseif($inventario->fecha_vencimiento <= now()->addDays(7))
                                                        <br><small class="text-warning"><strong>PRÓXIMO A VENCER</strong></small>
                                                    @endif
                                                </td>
                                                <td>{{ number_format($inventario->cantidad_original, 2) }}</td>
                                                <td>
                                                    <strong>{{ number_format($inventario->cantidad_actual, 2) }}</strong>
                                                    @if($inventario->cantidad_actual == 0)
                                                        <br><small class="text-muted">AGOTADO</small>
                                                    @endif
                                                </td>
                                                <td>{{ \App\Helpers\CurrencyHelper::format($inventario->precio_costo) }}</td>
                                                <td>
                                                    <strong>{{ \App\Helpers\CurrencyHelper::format($inventario->valor_total) }}</strong>
                                                </td>
                                                <td>
                                                    @if($inventario->estado == 'disponible')
                                                        <span class="badge bg-success">Disponible</span>
                                                    @elseif($inventario->estado == 'agotado')
                                                        <span class="badge bg-secondary">Agotado</span>
                                                    @elseif($inventario->estado == 'vencido')
                                                        <span class="badge bg-danger">Vencido</span>
                                                    @else
                                                        <span class="badge bg-warning">{{ ucfirst($inventario->estado) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $diasRestantes = $inventario->fecha_vencimiento->diffInDays(now(), false);
                                                    @endphp
                                                    @if($diasRestantes > 0)
                                                        <span class="text-danger">-{{ $diasRestantes }} días</span>
                                                    @else
                                                        <span class="text-success">{{ abs($diasRestantes) }} días</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center text-muted py-4">
                                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                                    <br>
                                                    No hay inventarios registrados para este producto
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if($inventarios->count() > 0)
                                        <tfoot class="table-dark">
                                            <tr>
                                                <td colspan="5"><strong>TOTALES</strong></td>
                                                <td><strong>{{ number_format($totalCantidad, 2) }}</strong></td>
                                                <td>-</td>
                                                <td><strong>{{ \App\Helpers\CurrencyHelper::format($valorTotal) }}</strong></td>
                                                <td colspan="2">-</td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    @if($inventarios->count() > 0)
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card border-info">
                                    <div class="card-body text-center">
                                        <h5 class="card-title text-info">Lote Más Antiguo</h5>
                                        <p class="card-text">
                                            <strong>{{ $inventarios->first()->lote }}</strong><br>
                                            Ingreso: {{ $inventarios->first()->fecha_ingreso->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                        <h5 class="card-title text-warning">Próximo a Vencer</h5>
                                        @php
                                            $proximoVencer = $inventarios->where('cantidad_actual', '>', 0)->sortBy('fecha_vencimiento')->first();
                                        @endphp
                                        @if($proximoVencer)
                                            <p class="card-text">
                                                <strong>{{ $proximoVencer->lote }}</strong><br>
                                                Vence: {{ $proximoVencer->fecha_vencimiento->format('d/m/Y') }}
                                            </p>
                                        @else
                                            <p class="card-text text-muted">Sin stock disponible</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <h5 class="card-title text-success">Rotación</h5>
                                        <p class="card-text">
                                            @php
                                                $lotesMasAntiguo = $inventarios->where('cantidad_actual', '>', 0)->sortBy('fecha_ingreso')->first();
                                                $diasEnInventario = $lotesMasAntiguo ? $lotesMasAntiguo->fecha_ingreso->diffInDays(now()) : 0;
                                            @endphp
                                            <strong>{{ $diasEnInventario }} días</strong><br>
                                            en inventario
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Acciones -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center">
                                    <button class="btn btn-primary no-print" onclick="window.print()">
                                        <i class="fas fa-print mr-2"></i>
                                        Imprimir Reporte
                                    </button>
                                    <a href="{{ url()->previous() }}" class="btn btn-secondary ml-2 no-print">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Volver
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
        page-break-inside: avoid;
    }
    
    .container-fluid {
        padding: 0 !important;
    }
    
    .table {
        font-size: 0.9rem;
    }
}
</style>
@endsection
