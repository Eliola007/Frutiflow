<?php

namespace App\Helpers;

class TicketHelper
{
    /**
     * Información del negocio para tickets
     */
    public static function getBusinessInfo(): array
    {
        return [
            'name' => config('app.business_name', 'FRUTIFLOW'),
            'slogan' => config('app.business_slogan', 'Frutas Frescas y de Calidad'),
            'address' => config('app.business_address', 'Av. Principal #123, Centro'),
            'city' => config('app.business_city', 'Ciudad, Estado CP 12345'),
            'phone' => config('app.business_phone', '(555) 123-4567'),
            'email' => config('app.business_email', 'ventas@frutiflow.com'),
            'website' => config('app.business_website', 'www.frutiflow.com'),
            'tax_id' => config('app.business_tax_id', 'RFC: XXXX000000XXX'),
        ];
    }

    /**
     * Mensaje de agradecimiento aleatorio
     */
    public static function getRandomThankYouMessage(): string
    {
        $messages = [
            '¡Gracias por su compra!',
            '¡Vuelva pronto!',
            '¡Disfrute sus frutas frescas!',
            '¡Gracias por preferirnos!',
            '¡Hasta la próxima!',
            '¡Que tenga un excelente día!',
            '¡Gracias por su confianza!',
            '¡Esperamos verle de nuevo!'
        ];

        return $messages[array_rand($messages)];
    }

    /**
     * Formatear método de pago para mostrar
     */
    public static function formatPaymentMethod(string $method): string
    {
        $methods = [
            'efectivo' => 'EFECTIVO',
            'tarjeta' => 'TARJETA',
            'transferencia' => 'TRANSFERENCIA',
            'credito' => 'CRÉDITO',
            'vale' => 'VALE',
            'otro' => 'OTRO'
        ];

        return $methods[$method] ?? strtoupper($method);
    }

    /**
     * Crear código QR con información de la venta (para futuras implementaciones)
     */
    public static function generateQRData($venta): string
    {
        return json_encode([
            'ticket' => $venta->numero_interno,
            'fecha' => $venta->fecha_venta->format('Y-m-d H:i:s'),
            'total' => $venta->total,
            'items' => $venta->items->count(),
            'metodo' => $venta->metodo_pago
        ]);
    }

    /**
     * Formatear número de teléfono para mostrar
     */
    public static function formatPhone(string $phone): string
    {
        // Remover caracteres no numéricos
        $clean = preg_replace('/[^0-9]/', '', $phone);
        
        // Formatear según longitud
        if (strlen($clean) === 10) {
            return '(' . substr($clean, 0, 3) . ') ' . substr($clean, 3, 3) . '-' . substr($clean, 6);
        }
        
        return $phone;
    }

    /**
     * Obtener configuración de impresión según tipo
     */
    public static function getPrintConfig(string $type = 'normal'): array
    {
        $configs = [
            'normal' => [
                'width' => '80mm',
                'font_size' => '12px',
                'line_height' => '1.4',
                'margin' => '5mm'
            ],
            'compact' => [
                'width' => '58mm',
                'font_size' => '10px',
                'line_height' => '1.3',
                'margin' => '2mm'
            ],
            'thermal' => [
                'width' => '48mm',
                'font_size' => '8px',
                'line_height' => '1.2',
                'margin' => '1mm'
            ]
        ];

        return $configs[$type] ?? $configs['normal'];
    }
}
