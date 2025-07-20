<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\MyOrderResource\Pages;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyOrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'My Orders';

    protected static ?string $navigationLabel = 'Received Orders';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('orderItems.menuItem', function (Builder $query) {
                $query->where('user_id', auth()->user()?->getKey());
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('customer_name')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('customer_phone')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('customer_email')
                            ->disabled(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Order Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'preparing' => 'Preparing',
                                'ready' => 'Ready',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->live(),
                        
                        Forms\Components\DateTimePicker::make('estimated_completion_time'),
                        
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->visible(fn (Forms\Get $get) => $get('status') === 'completed'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->disabled()
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('special_instructions')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'dine_in' => 'primary',
                        'takeaway' => 'success',
                        'online' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending', 'cancelled' => 'danger',
                        'confirmed', 'preparing' => 'warning',
                        'ready' => 'primary',
                        'completed' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('orderItems')
                    ->label('My Items')
                    ->formatStateUsing(function ($record) {
                        $myItems = $record->orderItems->filter(function ($item) {
                            return $item->menuItem && $item->menuItem->user_id === auth()->user()?->getKey();
                        });
                        return $myItems->count() . ' item(s)';
                    }),
                Tables\Columns\TextColumn::make('my_total')
                    ->label('My Total')
                    ->formatStateUsing(function ($record) {
                        $myTotal = $record->orderItems->filter(function ($item) {
                            return $item->menuItem && $item->menuItem->user_id === auth()->user()?->getKey();
                        })->sum('total_price');
                        return '₱' . number_format($myTotal, 2);
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimated_completion_time')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('order_type'),
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $q, $date): Builder => $q->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $q, $date): Builder => $q->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Remove delete action for tenants
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyOrders::route('/'),
            'view' => Pages\ViewMyOrder::route('/{record}'),
            'edit' => Pages\EditMyOrder::route('/{record}/edit'),
        ];
    }
}