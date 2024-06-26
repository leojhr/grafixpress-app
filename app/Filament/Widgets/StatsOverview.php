<?php

namespace App\Filament\Widgets;

use App\Models\SaleProduct;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $dayEarnings = SaleProduct::selectRaw('SUM(sale_products.quantity * inventories.sale_price) as total_amount')
            ->join('inventories', 'sale_products.product_id', '=', 'inventories.id')
            ->whereDate('sale_products.created_at', $today)
            ->value('total_amount');

        if (!$dayEarnings) $dayEarnings = 0;

        $weekEarnings = SaleProduct::selectRaw('SUM(sale_products.quantity * inventories.sale_price) as total_amount')
            ->join('inventories', 'sale_products.product_id', '=', 'inventories.id')
            ->whereBetween('sale_products.created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->value('total_amount');

        if (!$weekEarnings) $weekEarnings = 0;

        $monthEarnings = SaleProduct::selectRaw('SUM(sale_products.quantity * inventories.sale_price) as total_amount')
            ->join('inventories', 'sale_products.product_id', '=', 'inventories.id')
            ->whereBetween('sale_products.created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->value('total_amount');

        if (!$monthEarnings) $monthEarnings = 0;

        return [
            Stat::make('Ganancias de hoy', '$ ' . $dayEarnings ?? 0),
            Stat::make('Ganancias de esta semana', '$ ' . $weekEarnings ?? 0),
            Stat::make('Ganancias de este mes', '$ ' . $monthEarnings ?? 0),
        ];
    }
}
