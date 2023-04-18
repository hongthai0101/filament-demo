<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Carbon\Carbon;
use Filament\Widgets\BarChartWidget;

class CustomerChart extends BarChartWidget
{
    protected static ?string $heading = 'Customers';

    protected function getData(): array {
        $customers = Customer::select('created_at')->get()->groupBy(function($customers) {
            return Carbon::parse($customers->created_at)->format('F');
        });
        $quantities = [];
        foreach ($customers as $customer => $value) {
            $quantities[] = $value->count();
        }
        return [
            'datasets' => [
                [
                    'label' => 'Customers Joined',
                    'data' => $quantities,
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 205, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(201, 203, 207, 0.2)'
                    ],
                    'borderColor' => [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)'
                    ],
                    'borderWidth' => 1
                ],
            ],
            'labels' => $customers->keys(),
        ];
    }
}
