<?php

namespace App\Filament\Admin\Resources\CompraResource\Pages;

use App\Filament\Admin\Resources\CompraResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateCompra extends CreateRecord
{
    protected static string $resource = CompraResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = \Illuminate\Support\Facades\Auth::id();
        $data['estado'] = 'pendiente';
        
        // Calcular total general
        $total = 0;
        if (isset($data['items'])) {
            foreach ($data['items'] as &$item) {
                // Generar lote automáticamente si no se proporciona
                if (empty($item['lote'])) {
                    $item['lote'] = $this->generarLote();
                }
                
                $total += $item['subtotal'] ?? 0;
            }
        }
        $data['total'] = $total;
        
        return $data;
    }
    
    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Compra Registrada')
            ->body('La compra se ha registrado exitosamente con ' . $this->record->items->count() . ' producto(s).')
            ->success()
            ->send();
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    private function generarLote(): string
    {
        $ultimoLote = \App\Models\CompraItem::whereNotNull('lote')
            ->where('lote', 'like', 'LOTE-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($ultimoLote && preg_match('/LOTE-(\d+)-(\d{4})/', $ultimoLote->lote, $matches)) {
            $numero = intval($matches[1]) + 1;
            $year = $matches[2];
            
            // Si cambió el año, reiniciar numeración
            if ($year != now()->year) {
                $numero = 1;
                $year = now()->year;
            }
        } else {
            $numero = 1;
            $year = now()->year;
        }

        return sprintf('LOTE-%03d-%s', $numero, $year);
    }
}
