<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Generar ticket de venta para impresión
     */
    public function ticket(Venta $venta): View
    {
        // Cargar relaciones necesarias
        $venta->load(['cliente', 'items.producto', 'vendedor']);
        
        return view('tickets.venta', compact('venta'));
    }

    /**
     * Generar ticket de venta en formato compacto (58mm)
     */
    public function ticketCompacto(Venta $venta): View
    {
        // Cargar relaciones necesarias
        $venta->load(['cliente', 'items.producto', 'vendedor']);
        
        return view('tickets.venta-compacto', compact('venta'));
    }

    /**
     * Previsualizar ticket antes de imprimir
     */
    public function preview(Venta $venta): View
    {
        // Cargar relaciones necesarias
        $venta->load(['cliente', 'items.producto', 'vendedor']);
        
        return view('tickets.preview', compact('venta'));
    }
}
