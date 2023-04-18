<?php

namespace App\Filament\Resources\Customer\CustomerResource\Widgets;

use Filament\Widgets\Widget;

class CustomerOverview extends Widget
{
    protected static string $view = 'filament.resources.customer-resource.widgets.customer-overview';

    protected function getViewData(): array
    {
        return [
            'customer' => 1,
        ];
    }
}
