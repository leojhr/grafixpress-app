<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Models\Inventory;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
use Filament\Infolists\Infolist;
use App\Filament\Resources\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $label = 'Venta';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make(label: 'Detalles de la venta')->schema([
                        Forms\Components\TextInput::make('order_number')->default('GX-' . random_int(1000000, 9999999))->disabled()->dehydrated()->required(),

                        Forms\Components\Select::make('payment_method')
                            ->required()
                            ->options([
                                'pagomovil' => 'Pago móvil',
                                'tarjeta' => 'Punto de Venta',
                                'efectivo' => 'Efectivo',
                            ])->label('Método de pago'),
                    ])->columns(2),
                    Forms\Components\Wizard\Step::make(label: 'Articulos')->schema([
                        Forms\Components\Repeater::make('SaleProducts')->relationship()->columns(2)->columnStart(1)->schema([
                            Forms\Components\Select::make('product_id')
                                ->required()
                                ->reactive()
                                ->label('Producto')
                                ->options(
                                    Inventory::query()->pluck(column: 'product_name', key: 'id')
                                )->searchable()->afterStateUpdated(fn ($state, Forms\Set $set) =>
                                $set('sale_price', Inventory::find($state)?->sale_price ?? 0)),

                            Forms\Components\TextInput::make('sale_price')
                                ->label('Precio unitario')
                                ->disabled()
                                ->dehydrated()
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('quantity')->numeric()->default(1)->required()->live()->dehydrated()->label('Cantidad'),

                            Forms\Components\Placeholder::make('total_product')
                                ->label('Precio final')
                                ->content(function ($get, $set) {
                                    $result = $get('quantity') * $get('sale_price');
                                    $set('total', $get('total') + $result);
                                    return $result;
                                })
                        ])->columnSpan(3)->itemLabel('')->label('Lista')->collapsible(),

                        Forms\Components\Placeholder::make('total')->label('Total a cobrar')->disabled()->live()->content(function ($get) {



                            return $get('SaleProducts.product_id');
                        })->afterStateUpdated(fn ($set, ?string $state) => $set('total', $state))
                    ])->columns(4)

                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment_method')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()->money()
                    ]),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha de Venta')->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\EditAction::make(),
                ]),

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
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}
