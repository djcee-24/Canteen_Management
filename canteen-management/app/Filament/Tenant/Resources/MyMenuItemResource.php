<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\MyMenuItemResource\Pages;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyMenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'My Menu';

    protected static ?string $navigationLabel = 'My Menu Items';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->user()?->getKey());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Set $set) {
                                $set('slug', \Illuminate\Support\Str::slug($state));
                            }),
                        
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(MenuItem::class, 'slug', ignoreRecord: true),
                        
                        Forms\Components\Select::make('menu_category_id')
                            ->label('Category')
                            ->options(MenuCategory::where('is_active', true)->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                        
                        Forms\Components\Hidden::make('user_id')
                            ->default(auth()->user()?->getKey()),
                        
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('₱')
                            ->step(0.01),
                        
                        Forms\Components\TextInput::make('preparation_time')
                            ->required()
                            ->numeric()
                            ->suffix('minutes')
                            ->default(15),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Description & Media')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('menu-items')
                            ->visibility('public'),
                        
                        Forms\Components\Textarea::make('ingredients')
                            ->maxLength(65535)
                            ->helperText('List the main ingredients'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Settings & Details')
                    ->schema([
                        Forms\Components\Toggle::make('is_available')
                            ->required()
                            ->default(true),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->default(false),
                        
                        Forms\Components\TextInput::make('calories')
                            ->numeric()
                            ->suffix('cal'),
                        
                        Forms\Components\TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0),
                        
                        Forms\Components\TagsInput::make('allergens')
                            ->helperText('Common allergens: nuts, dairy, gluten, etc.'),
                        
                        Forms\Components\TagsInput::make('dietary_info')
                            ->helperText('e.g., vegetarian, vegan, gluten-free'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('menuCategory.name')
                    ->label('Category')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('preparation_time')
                    ->numeric()
                    ->suffix(' min')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_available')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('menu_category_id')
                    ->label('Category')
                    ->options(MenuCategory::pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_available'),
                Tables\Filters\TernaryFilter::make('is_featured'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyMenuItems::route('/'),
            'create' => Pages\CreateMyMenuItem::route('/create'),
            'edit' => Pages\EditMyMenuItem::route('/{record}/edit'),
        ];
    }
}