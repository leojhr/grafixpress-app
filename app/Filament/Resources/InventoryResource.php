<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryResource\Pages;
use App\Filament\Resources\InventoryResource\RelationManagers;
use App\Models\Inventory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-s-rectangle-group';

    protected static ?string $label = 'Inventario';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('product_name')->required()->placeholder('Ingresa el nombre del producto.')
                    ->maxLength(128)->label('Nombre del producto'),
                Forms\Components\TextInput::make('quantity')->required()->numeric()->label('Cantidad')->default(0),
                Forms\Components\TextInput::make('supply_cost')->required()->numeric()->label('Precio del proveedor'),
                Forms\Components\TextInput::make('sale_price')->required()->numeric()->label('Precio de venta'),
                Forms\Components\RichEditor::make('description')->placeholder('Descripción del producto')->label('Descripción')->columnSpanFull(),
                Forms\Components\Checkbox::make('is_service')->inline()->label('¿Es un servicio?')->default(false),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product_name')->searchable()->label('Nombre del Producto'),
                TextColumn::make('quantity')->sortable()->label('Cantidad'),
                TextColumn::make('sale_price')->sortable()->label('Precio')->money(),
                TextColumn::make('supply_date')->sortable()->label('Fecha de abastecimiento'),
                CheckboxColumn::make('is_service')->disabled()->sortable()->label('Es servicio')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListInventories::route('/'),
            'create' => Pages\CreateInventory::route('/create'),
            'edit' => Pages\EditInventory::route('/{record}/edit'),
        ];
    }
}
