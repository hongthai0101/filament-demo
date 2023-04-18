<?php

namespace App\Filament\Resources\Customer;

use App\Filament\Resources\Customer\CustomerResource\Pages\ManageCustomers;
use App\Models\Customer;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make([
                    TextInput::make('name')->required(),
                    TextInput::make('email')->unique()->email()->required(),
                    TextInput::make('phone')->unique()->tel()->required(),
                    TextInput::make('address')->string()->maxValue(255)->required(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(isIndividual:true)
                    ->sortable()
                    ->extraAttributes(['class' => 'xl']),
                TextColumn::make('email')
                    ->searchable(isIndividual:true)
                    ->sortable(),
                TextColumn::make('phone'),
                TextColumn::make('address'),
                TextColumn::make('created_at')
                ->dateTime('d-M-Y')
                ->sortable()
                ->label('Created At'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCustomers::route('/'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }
}
