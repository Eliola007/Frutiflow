<?php

namespace App\Filament\Admin\Resources\ProductoResource\Pages;

use App\Filament\Admin\Resources\ProductoResource;
use Filament\Resources\Pages\Page;
use Filament\Actions;

class ProductosDashboard extends Page
{
    protected static string $resource = ProductoResource::class;

    protected static string $view = 'filament.admin.resources.producto-resource.pages.productos-dashboard';
    
    protected static ?string $title = 'Dashboard de Productos';
    
    protected static ?string $navigationLabel = 'Dashboard';
    
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('crear_producto')
                ->label('Nuevo Producto')
                ->icon('heroicon-o-plus')
                ->url(ProductoResource::getUrl('create'))
                ->color('primary'),
                
            Actions\Action::make('lista_productos')
                ->label('Ver Lista')
                ->icon('heroicon-o-list-bullet')
                ->url(ProductoResource::getUrl('index'))
                ->color('gray'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProductoResource\Widgets\ProductosOverviewWidget::class,
        ];
    }

    protected function getWidgets(): array
    {
        return [
            ProductoResource\Widgets\ProductosStockChart::class,
            ProductoResource\Widgets\ProductosGrupoChart::class,
            ProductoResource\Widgets\InventarioValorChart::class,
        ];
    }
}
