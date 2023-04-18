<?php

namespace App\Filament\Resources\AdminManagement\RoleResource\Pages;

use App\Filament\Resources\AdminManagement\RoleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
