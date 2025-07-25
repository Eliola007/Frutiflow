<?php

namespace App\Filament\Admin\Resources\InventarioResource\Pages;

use App\Filament\Admin\Resources\InventarioResource;
use App\Models\Inventario;
use App\Models\Producto;
use Filament\Resources\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Radio;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AjustesInventario extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = InventarioResource::class;

    protected static string $view = 'filament.admin.resources.inventario-resource.pages.ajustes-inventario';

    protected static ?string $title = 'Ajustes de Inventario';

    protected static ?string $navigationLabel = 'Ajustes';

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Ajuste de Stock')
                    ->description('Realiza ajustes de inventario por diferencias físicas, mermas o correcciones')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('producto_id')
                                    ->label('Producto')
                                    ->searchable()
                                    ->options(Producto::pluck('nombre', 'id'))
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $lotes = Inventario::where('producto_id', $state)
                                                ->where('cantidad_actual', '>', 0)
                                                ->where('estado', '!=', 'vencido')
                                                ->orderBy('fecha_ingreso')
                                                ->get()
                                                ->mapWithKeys(function ($lote) {
                                                    return [
                                                        $lote->id => "Lote: {$lote->lote} | Stock: {$lote->cantidad_actual} | Ingreso: {$lote->fecha_ingreso->format('d/m/Y')}"
                                                    ];
                                                });
                                            
                                            $set('lote_opciones', $lotes->toArray());
                                        }
                                    }),

                                Select::make('inventario_id')
                                    ->label('Lote')
                                    ->required()
                                    ->searchable()
                                    ->options(function (callable $get) {
                                        $productoId = $get('producto_id');
                                        if (!$productoId) return [];
                                        
                                        return Inventario::where('producto_id', $productoId)
                                            ->where('cantidad_actual', '>', 0)
                                            ->where('estado', '!=', 'vencido')
                                            ->orderBy('fecha_ingreso')
                                            ->get()
                                            ->mapWithKeys(function ($lote) {
                                                return [
                                                    $lote->id => "Lote: {$lote->lote} | Stock: {$lote->cantidad_actual} | Ingreso: {$lote->fecha_ingreso->format('d/m/Y')}"
                                                ];
                                            });
                                    })
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $lote = Inventario::find($state);
                                            if ($lote) {
                                                $set('stock_actual', $lote->cantidad_actual);
                                            }
                                        }
                                    }),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('stock_actual')
                                    ->label('Stock Actual en Sistema')
                                    ->disabled()
                                    ->numeric(),

                                TextInput::make('stock_fisico')
                                    ->label('Stock Físico Real')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $stockActual = $get('stock_actual') ?? 0;
                                        $diferencia = $state - $stockActual;
                                        $set('diferencia', $diferencia);
                                        $set('tipo_ajuste', $diferencia >= 0 ? 'incremento' : 'decremento');
                                    }),

                                TextInput::make('diferencia')
                                    ->label('Diferencia')
                                    ->disabled()
                                    ->numeric()
                                    ->formatStateUsing(function ($state) {
                                        if (is_null($state)) return '';
                                        return ($state >= 0 ? '+' : '') . $state;
                                    }),
                            ]),

                        Radio::make('tipo_ajuste')
                            ->label('Tipo de Ajuste')
                            ->options([
                                'incremento' => 'Incremento (encontrado más stock)',
                                'decremento' => 'Decremento (merma, pérdida, robo)',
                                'correccion' => 'Corrección de inventario',
                            ])
                            ->required()
                            ->inline()
                            ->disabled(),

                        Textarea::make('motivo')
                            ->label('Motivo del Ajuste')
                            ->required()
                            ->placeholder('Describe el motivo del ajuste de inventario...')
                            ->rows(3),

                        Textarea::make('observaciones')
                            ->label('Observaciones Adicionales')
                            ->placeholder('Información adicional sobre el ajuste...')
                            ->rows(2),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('procesar_ajuste')
                ->label('Procesar Ajuste')
                ->color('primary')
                ->icon('heroicon-o-check')
                ->requiresConfirmation()
                ->modalHeading('Confirmar Ajuste de Inventario')
                ->modalDescription(function () {
                    $data = $this->form->getState();
                    $diferencia = $data['diferencia'] ?? 0;
                    $tipo = $diferencia >= 0 ? 'incrementará' : 'decrementará';
                    $cantidad = abs($diferencia);
                    
                    return "Este ajuste {$tipo} el stock en {$cantidad} unidades. Esta acción no se puede deshacer.";
                })
                ->action(function () {
                    $this->procesarAjuste();
                }),

            Action::make('cancelar')
                ->label('Cancelar')
                ->color('gray')
                ->url(InventarioResource::getUrl('index')),
        ];
    }

    public function procesarAjuste(): void
    {
        $data = $this->form->getState();
        
        try {
            $inventario = Inventario::findOrFail($data['inventario_id']);
            $diferencia = $data['diferencia'];
            $stockAnterior = $inventario->cantidad_actual;
            $stockNuevo = $data['stock_fisico'];

            // Validar que el nuevo stock no sea negativo
            if ($stockNuevo < 0) {
                Notification::make()
                    ->title('Error')
                    ->body('El stock no puede ser negativo')
                    ->danger()
                    ->send();
                return;
            }

            // Actualizar el inventario
            $inventario->update([
                'cantidad_actual' => $stockNuevo,
            ]);

            // Registrar el movimiento (si tienes una tabla de movimientos)
            // Aquí podrías registrar en una tabla de auditoría o movimientos

            // Log del cambio
            Log::info('Ajuste de inventario realizado', [
                'inventario_id' => $inventario->id,
                'producto' => $inventario->producto->nombre,
                'lote' => $inventario->lote,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'diferencia' => $diferencia,
                'motivo' => $data['motivo'],
                'observaciones' => $data['observaciones'] ?? null,
                'usuario' => Auth::user()->name,
                'fecha' => now(),
            ]);

            // Notificación de éxito
            Notification::make()
                ->title('Ajuste procesado exitosamente')
                ->body("Stock actualizado de {$stockAnterior} a {$stockNuevo} unidades")
                ->success()
                ->send();

            // Limpiar formulario
            $this->form->fill([]);
            
            // Redireccionar
            $this->redirect(InventarioResource::getUrl('index'));

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al procesar ajuste')
                ->body('Ocurrió un error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getSubheading(): ?string
    {
        return 'Realiza ajustes de stock por diferencias físicas, mermas o correcciones de inventario';
    }
}
