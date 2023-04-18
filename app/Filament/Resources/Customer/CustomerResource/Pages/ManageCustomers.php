<?php

namespace App\Filament\Resources\Customer\CustomerResource\Pages;

use App\Filament\Resources\Customer\CustomerResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCustomers extends ManageRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CustomerResource\Widgets\CustomerOverview::class
        ];
    }

}
