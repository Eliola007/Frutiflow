<?php

namespace App\Filament\Admin\Resources\ProductoResource\Pages;

use App\Filament\Admin\Resources\ProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductos extends ListRecords
{
    protected static string $resource = ProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProductoResource\Widgets\ProductosOverviewWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            ProductoResource\Widgets\ProductosStockChart::class,
            ProductoResource\Widgets\ProductosGrupoChart::class,
            ProductoResource\Widgets\InventarioValorChart::class,
        ];
    }

    protected function getWidgets(): array
    {
        return array_merge(
            $this->getHeaderWidgets(),
            $this->getFooterWidgets()
        );
    }
}
