<?php

namespace App\Filament\Resources\Shop;

use App\Filament\Resources\Shop\OrderResource\Pages;
use App\Filament\Resources\Shop\OrderResource\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\Shop\OrderResource\Widgets\OrderOverview;
use App\Forms\Components\AddressForm;
use App\Models\Shop\Order;
use App\Models\Shop\Product;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Squire\Models\Currency;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Shop Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Card::make()
                            ->schema([
                                TextInput::make('number')
                                    ->default('OR-' . random_int(100000, 999999))
                                    ->disabled()
                                    ->required(),

                                Select::make('customer_id')
                                    ->relationship('customer', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        TextInput::make('name')->required(),
                                        TextInput::make('email')->unique()->email()->required(),
                                        TextInput::make('phone')->unique()->tel()->required(),
                                        TextInput::make('address')->string()->maxValue(255)->required(),
                                    ])
                                    ->createOptionAction(function (Action $action) {
                                        return $action
                                            ->modalHeading('Create customer')
                                            ->modalButton('Create customer')
                                            ->modalWidth('lg');
                                    }),

                                Select::make('status')
                                    ->options([
                                        'new' => 'New',
                                        'processing' => 'Processing',
                                        'shipped' => 'Shipped',
                                        'delivered' => 'Delivered',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->required(),

                                Select::make('currency')
                                    ->searchable()
                                    ->getSearchResultsUsing(fn (string $query) => Currency::where('name', 'like', "%{$query}%")->pluck('name', 'id'))
                                    ->getOptionLabelUsing(fn ($value): ?string => Currency::find($value)?->getAttribute('name'))
                                    ->required(),

                                AddressForm::make('address')
                                    ->columnSpan('full'),

                                MarkdownEditor::make('notes')
                                    ->columnSpan('full'),
                            ])
                            ->columns(2),

                        Section::make('Order items')
                            ->schema([
                                Repeater::make('items')
                                    ->relationship()
                                    ->schema([
                                        Select::make('product_id')
                                            ->label('Product')
                                            ->options(Product::query()->pluck('name', 'id'))
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(fn ($state, callable $set) => $set('unit_price', Product::find($state)?->price ?? 0))
                                            ->columnSpan([
                                                'md' => 5,
                                            ]),

                                        TextInput::make('qty')
                                            ->numeric()
                                            ->default(1)
                                            ->columnSpan([
                                                'md' => 2,
                                            ])
                                            ->required(),

                                        TextInput::make('unit_price')
                                            ->label('Unit Price')
                                            ->disabled()
                                            ->numeric()
                                            ->required()
                                            ->columnSpan([
                                                'md' => 3,
                                            ]),
                                    ])
                                    ->dehydrated()
                                    ->orderable()
                                    ->defaultItems(1)
                                    ->disableLabel()
                                    ->columns([
                                        'md' => 10,
                                    ])
                                    ->createItemButtonLabel('Add item')
                                    ->required(),
                            ]),
                    ])
                    ->columnSpan(['lg' => fn (?Order $record) => $record === null ? 3 : 2]),

                Card::make()
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (Order $record): string => $record->created_at->diffForHumans()),

                        Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (Order $record): string => $record->updated_at->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?Order $record) => $record === null),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->enum([
                        'new' => 'New',
                        'cancelled' => 'Cancelled',
                        'processing' => 'Processing',
                        'delivered' => 'Delivered',
                        'shipped' => 'Shipped'
                    ])
                    ->colors([
                        'primary' => 'new',
                        'danger' => 'cancelled',
                        'warning' => 'processing',
                        'success' => fn ($state) => in_array($state, ['delivered', 'shipped']),
                    ]),
                Tables\Columns\TextColumn::make('currency')
                    ->getStateUsing(fn ($record): ?string => Currency::find($record->currency)?->name ?? null)
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipping_price')
                    ->label('Shipping cost')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->date()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'cancelled' => 'Cancelled',
                        'processing' => 'Processing',
                        'delivered' => 'Delivered',
                        'shipped' => 'Shipped'
                    ])
                    ->multiple(),

                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->placeholder(fn ($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        DatePicker::make('created_until')
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->action(function () {
                        Notification::make()
                            ->title('Now, now, don\'t be cheeky, leave some records for others to play with!')
                            ->warning()
                            ->send();
                    }),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            OrderOverview::class,
        ];
    }

    public static function getRelations(): array
    {
        return [
            PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScope(SoftDeletingScope::class);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['number', 'customer.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Order $record */

        return [
            'Customer' => optional($record->customer)->name,
        ];
    }

    protected static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['customer', 'items']);
    }

    protected static function getNavigationBadge(): ?string
    {
        return static::$model::where('status', 'new')->count();
    }
}
