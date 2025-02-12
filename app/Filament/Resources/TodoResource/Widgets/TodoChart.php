<?php

namespace App\Filament\Resources\TodoResource\Widgets;

use App\Models\Todo;
use Filament\Widgets\ChartWidget;

class TodoChart extends ChartWidget
{
    protected static ?string $heading = 'Todo Count';

    protected function getData(): array
    {
        $labels = Todo::query()
            ->selectRaw('MONTH(due_date) as month')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();
        
        $month_name = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Count Todo in a Month',
                    'data' => array_values($labels),
                ],
            ],
            'labels' => array_map(fn ($month) => $month_name[$month], array_keys($labels)),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
