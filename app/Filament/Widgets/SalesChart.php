<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Ventas anuales';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $data = $this->getsalesPerMonth();

        return [
            'datasets' => [
                [
                    'label' => 'Ventas realizadas',
                    'data' => $data['salesPerMonth']
                ]
            ],
            'labels' => $data['months']
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getsalesPerMonth(): array
    {
        setlocale(LC_TIME, 'es_ES');
        $now = Carbon::now();

        $salesPerMonth = [];
        $months = collect(range(1, 12))->map(function ($month) use ($now, &$salesPerMonth) {
            $count = Sale::whereMonth('created_at', Carbon::parse($now->month($month)->format('Y-m')))->count();
            $salesPerMonth[] = $count;

            return $now->month($month)->format('M');
        })->toArray();

        return [
            'salesPerMonth' => $salesPerMonth,
            'months' => $months,
        ];
    }
}
