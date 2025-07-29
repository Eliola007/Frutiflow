<?php

namespace App\Observers;

use App\Models\CorteCaja;

class CorteCajaObserver
{
    public function creating(CorteCaja $corteCaja)
    {
        // Asignar usuario actual si no está definido
        if (!$corteCaja->usuario_id) {
            $corteCaja->usuario_id = auth()->id();
        }
        
        // Calcular totales automáticamente
        $corteCaja->calcularTotalesDia();
    }

    public function updating(CorteCaja $corteCaja)
    {
        // Recalcular si cambia el efectivo inicial o la fecha
        if ($corteCaja->isDirty(['efectivo_inicial', 'fecha'])) {
            $corteCaja->calcularTotalesDia();
        }
    }
}
