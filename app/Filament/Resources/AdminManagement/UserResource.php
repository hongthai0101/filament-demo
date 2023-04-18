<?php

namespace App\Filament\Resources\AdminManagement;

use App\Filament\Resources\AdminManagement\UserResource\RelationManagers\RolesRelationManager;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Hash;
use function filled;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Admin Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Toggle::make('is_admin')
                            ->required(),
                        TextInput::make('email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        TextInput::make('password')
                            ->password()
                            ->maxLength(255)
                            ->dehydrateStateUsing(static fn (null|string $state): null|string =>
                            filled($state) ? Hash::make($state): null,
                            )->required(static fn (Page $livewire): bool =>
                                $livewire instanceof UserResource\Pages\CreateUser,
                            )->dehydrated(static fn (null|string $state): bool =>
                            filled($state),
                            )->label(static fn (Page $livewire): string =>
                            ($livewire instanceof UserResource\Pages\EditUser) ? 'New Password' : 'Password'
                            ),
                        CheckboxList::make('roles')
                            ->relationship('roles', 'name')
                            ->columns(2)
                            ->helperText('Only Choose One!')
                            ->required()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable(),
                IconColumn::make('is_admin')->sortable()->boolean(),
                TextColumn::make('roles.name')->sortable(),
                TextColumn::make('email')->sortable(),
                TextColumn::make('deleted_at')
                    ->dateTime('d-M-Y')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime('d-M-Y')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
                ForceDeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RolesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => UserResource\Pages\ListUsers::route('/'),
            'create' => UserResource\Pages\CreateUser::route('/create'),
            'edit' => UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
