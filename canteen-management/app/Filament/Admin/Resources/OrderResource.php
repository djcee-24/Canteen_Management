<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Order Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->required()
                            ->maxLength(255)
                            ->default(fn () => 'ORD-' . strtoupper(uniqid()))
                            ->disabled()
                            ->dehydrated(),
                        
                        Forms\Components\Select::make('customer_id')
                            ->label('Customer')
                            ->options(User::whereHas('roles', function ($query) {
                                $query->whereIn('name', ['customer', 'guest']);
                            })->pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),
                        
                        Forms\Components\TextInput::make('customer_name')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('customer_phone')
                            ->tel()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('customer_email')
                            ->email()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Order Details')
                    ->schema([
                        Forms\Components\Select::make('order_type')
                            ->options([
                                'dine_in' => 'Dine In',
                                'takeaway' => 'Takeaway',
                                'online' => 'Online',
                            ])
                            ->required()
                            ->default('dine_in'),
                        
                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'card' => 'Card',
                                'online' => 'Online Payment',
                                'bank_transfer' => 'Bank Transfer',
                            ])
                            ->required()
                            ->default('cash'),
                        
                        Forms\Components\Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->required()
                            ->default('pending'),
                        
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
                            ->default('pending')
                            ->live(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),
                        
                        Forms\Components\TextInput::make('tax_amount')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->default(0),
                        
                        Forms\Components\TextInput::make('discount_amount')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->default(0),
                        
                        Forms\Components\TextInput::make('total_amount')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('special_instructions')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        
                        Forms\Components\DateTimePicker::make('estimated_completion_time'),
                        
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->visible(fn (Forms\Get $get) => $get('status') === 'completed'),
                    ])
                    ->columns(2),
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
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Registered Customer')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending', 'failed' => 'danger',
                        'paid' => 'success',
                        'refunded' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('USD')
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('payment_status'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}