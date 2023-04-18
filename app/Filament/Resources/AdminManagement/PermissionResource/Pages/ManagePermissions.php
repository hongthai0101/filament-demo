<?php

namespace App\Filament\Resources\AdminManagement\PermissionResource\Pages;

use App\Filament\Resources\AdminManagement\PermissionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePermissions extends ManageRecords
{
    protected static string $resource = PermissionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
