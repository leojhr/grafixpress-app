<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\SaleResource;
use App\Models\SaleProduct;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestSales extends BaseWidget
{
    protected static ?string $heading = "Últimas ventas";

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(SaleResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->sortable()
                    ->label('Número de factura'),

                Tables\Columns\TextColumn::make('total')
                    ->sortable()
                    ->label('Monto')
                    ->money('USD'),

                Tables\Columns\TextColumn::make('created_at')
                    ->time()
                    ->label('Hora'),
            ]);
    }
}
