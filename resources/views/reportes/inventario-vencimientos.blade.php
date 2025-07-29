@extends('layouts.app')

@section('title', 'Reporte de Vencimientos de Inventario')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Reporte de Vencimientos de Inventario
                    </h3>
                    <div class="card-tools">
                        <span class="text-muted">
                            Generado el {{ now()->format('d/m/Y H:i:s') }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- Resumen -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $vencidos->count() }}</h3>
                                    <p>Lotes Vencidos</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $proximosVencer->count() }}</h3>
                                    <p>Próximos a Vencer</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ \App\Helpers\CurrencyHelper::format($valorVencidos) }}</h3>
                                    <p>Valor Vencido</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-orange">
                                <div class="inner">
                                    <h3>{{ \App\Helpers\CurrencyHelper::format($valorProximos) }}</h3>
                                    <p>Valor en Riesgo</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Productos Vencidos -->
                    <div class="card mb-4">
                        <div class="card-header bg-danger text-white">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-times-circle mr-2"></i>
                                Productos Vencidos
                            </h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Lote</th>
                                            <th>Fecha Vencimiento</th>
                                            <th>Días Vencido</th>
                                            <th>Cantidad</th>
                                            <th>Precio Costo</th>
                                            <th>Valor Total</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($vencidos as $inventario)
                                            <tr class="table-danger">
                                                <td>
                                                    <strong>{{ $inventario->producto->nombre }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $inventario->producto->codigo }}</small>
                                                </td>
                                                <td>{{ $inventario->lote }}</td>
                                                <td>{{ $inventario->fecha_vencimiento->format('d/m/Y') }}</td>
                                                <td>
                                                    <span class="badge bg-danger">
                                                        {{ $inventario->fecha_vencimiento->diffInDays(now()) }} días
                                                    </span>
                                                </td>
                                                <td>{{ number_format($inventario->cantidad_actual, 2) }} {{ $inventario->producto->unidad_medida }}</td>
                                                <td>{{ \App\Helpers\CurrencyHelper::format($inventario->precio_costo) }}</td>
                                                <td>
                                                    <strong>{{ \App\Helpers\CurrencyHelper::format($inventario->valor_total) }}</strong>
                                                </td>
                                                <td>
                                                    @if($inventario->observaciones)
                                                        {{ $inventario->observaciones }}
                                                    @else
                                                        <span class="text-muted">Sin observaciones</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-success py-4">
                                                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                                                    <br>
                                                    ¡Excelente! No hay productos vencidos
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if($vencidos->count() > 0)
                                        <tfoot class="table-dark">
                                            <tr>
                                                <td colspan="6"><strong>TOTAL VENCIDOS</strong></td>
                                                <td><strong>{{ \App\Helpers\CurrencyHelper::format($valorVencidos) }}</strong></td>
                                                <td>-</td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Productos Próximos a Vencer -->
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-clock mr-2"></i>
                                Productos Próximos a Vencer (30 días)
                            </h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Lote</th>
                                            <th>Fecha Vencimiento</th>
                                            <th>Días Restantes</th>
                                            <th>Cantidad</th>
                                            <th>Precio Costo</th>
                                            <th>Valor Total</th>
                                            <th>Prioridad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($proximosVencer as $inventario)
                                            @php
                                                $diasRestantes = abs($inventario->fecha_vencimiento->diffInDays(now(), false));
                                                $prioridad = 'baja';
                                                $claseFila = 'table-info';
                                                
                                                if ($diasRestantes <= 3) {
                                                    $prioridad = 'crítica';
                                                    $claseFila = 'table-danger';
                                                } elseif ($diasRestantes <= 7) {
                                                    $prioridad = 'alta';
                                                    $claseFila = 'table-warning';
                                                } elseif ($diasRestantes <= 15) {
                                                    $prioridad = 'media';
                                                    $claseFila = 'table-info';
                                                }
                                            @endphp
                                            <tr class="{{ $claseFila }}">
                                                <td>
                                                    <strong>{{ $inventario->producto->nombre }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $inventario->producto->codigo }}</small>
                                                </td>
                                                <td>{{ $inventario->lote }}</td>
                                                <td>{{ $inventario->fecha_vencimiento->format('d/m/Y') }}</td>
                                                <td>
                                                    @if($diasRestantes == 0)
                                                        <span class="badge bg-danger">HOY</span>
                                                    @elseif($diasRestantes < 0)
                                                        <span class="badge bg-danger">VENCIDO</span>
                                                    @else
                                                        <span class="badge 
                                                            @if($diasRestantes <= 3) bg-danger
                                                            @elseif($diasRestantes <= 7) bg-warning
                                                            @else bg-info
                                                            @endif
                                                        ">
                                                            {{ $diasRestantes }} días
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>{{ number_format($inventario->cantidad_actual, 2) }} {{ $inventario->producto->unidad_medida }}</td>
                                                <td>{{ \App\Helpers\CurrencyHelper::format($inventario->precio_costo) }}</td>
                                                <td>
                                                    <strong>{{ \App\Helpers\CurrencyHelper::format($inventario->valor_total) }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge 
                                                        @if($prioridad == 'crítica') bg-danger
                                                        @elseif($prioridad == 'alta') bg-warning
                                                        @elseif($prioridad == 'media') bg-info
                                                        @else bg-secondary
                                                        @endif
                                                    ">
                                                        {{ ucfirst($prioridad) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-success py-4">
                                                    <i class="fas fa-smile fa-3x mb-3"></i>
                                                    <br>
                                                    No hay productos próximos a vencer en los próximos 30 días
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if($proximosVencer->count() > 0)
                                        <tfoot class="table-dark">
                                            <tr>
                                                <td colspan="6"><strong>TOTAL PRÓXIMOS A VENCER</strong></td>
                                                <td><strong>{{ \App\Helpers\CurrencyHelper::format($valorProximos) }}</strong></td>
                                                <td>-</td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Recomendaciones -->
                    @if($vencidos->count() > 0 || $proximosVencer->count() > 0)
                        <div class="card mt-4 border-info">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-lightbulb mr-2"></i>
                                    Recomendaciones
                                </h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    @if($vencidos->count() > 0)
                                        <li class="mb-2">
                                            <i class="fas fa-exclamation-triangle text-danger mr-2"></i>
                                            <strong>Productos Vencidos:</strong> Revisar y retirar del inventario {{ $vencidos->count() }} lotes vencidos por un valor de {{ \App\Helpers\CurrencyHelper::format($valorVencidos) }}.
                                        </li>
                                    @endif
                                    
                                    @php
                                        $criticos = $proximosVencer->filter(function($item) {
                                            return abs($item->fecha_vencimiento->diffInDays(now(), false)) <= 3;
                                        });
                                    @endphp
                                    
                                    @if($criticos->count() > 0)
                                        <li class="mb-2">
                                            <i class="fas fa-bolt text-danger mr-2"></i>
                                            <strong>Atención Crítica:</strong> {{ $criticos->count() }} lotes vencen en 3 días o menos. Priorizar su venta.
                                        </li>
                                    @endif
                                    
                                    @if($proximosVencer->count() > 0)
                                        <li class="mb-2">
                                            <i class="fas fa-chart-line text-warning mr-2"></i>
                                            <strong>Estrategia de Ventas:</strong> Implementar descuentos o promociones para productos próximos a vencer.
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-truck text-info mr-2"></i>
                                            <strong>Compras Futuras:</strong> Revisar frecuencia y cantidades de compra para reducir vencimientos.
                                        </li>
                                    @endif
                                </ul>
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
        font-size: 0.85rem;
    }
}
</style>
@endsection
