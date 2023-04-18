<?php

namespace App\Filament\Resources\AdminManagement\UserResource\Pages;

use App\Filament\Resources\AdminManagement\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
