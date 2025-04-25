<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GeoResource\Pages;
use App\Filament\Resources\GeoResource\RelationManagers;
use App\Models\Geo;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GeoResource extends Resource
{
    protected static ?string $model = Geo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('shipping_cost')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(10000000),
                TextInput::make('currency_name')
                    ->required(),
                TextInput::make('currency_value')
                    ->required()
                    ->numeric()
                    ->minValue(0,01)
                    ->maxValue(10000),
                Select::make('products')
                    ->relationship('products', 'name')
                    ->multiple()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('shipping_cost')->sortable()->searchable(),
                TextColumn::make('currency_name')->sortable()->searchable(),
                TextColumn::make('currency_value')->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGeos::route('/'),
            'create' => Pages\CreateGeo::route('/create'),
            'edit' => Pages\EditGeo::route('/{record}/edit'),
        ];
    }
}
