<?php

namespace App\Filament\Resources\AdminManagement\UserResource\Pages;

use App\Filament\Resources\AdminManagement\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
