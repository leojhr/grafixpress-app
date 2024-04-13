<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Models\Inventory;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
                                )->searchable()->afterStateUpdated(
                                    function ($state, Forms\Set $set, Forms\Get $get) {
                                        $product = Inventory::find($state);
                                        if ($product) {
                                            $set('sale_price', $product->sale_price);
                                        } else {
                                            $set('sale_price', 0);
                                        }
                                    }
                                ),

                            Forms\Components\TextInput::make('sale_price')
                                ->label('Precio unitario')
                                ->disabled()
                                ->default(fn ($get) => $get('product_id') ? Inventory::find($get('product_id'))?->sale_price ?? 0 : 0)
                                ->dehydrated()
                                ->numeric()
                                ->required(),

                            Forms\Components\TextInput::make('quantity')->numeric()->default(1)->required()->live()->dehydrated()->label('Cantidad')->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                $product = Inventory::find($get('product_id'));

                                if ($product->is_service == true) return;

                                if ($product->quantity < intval($get('quantity'))) {
                                    Notification::make()
                                        ->title('La cantidad ingresada no esta disponible.')
                                        ->danger()
                                        ->send();
                                    $set('quantity', 0);
                                };
                            }),

                            Forms\Components\Placeholder::make('total_product')
                                ->label('Precio final')
                                ->content(function ($get, $set) {

                                    $result = floatval($get('quantity')) * floatval($get('sale_price'));
                                    $set('total_product', $result);
                                    return "$" . $result;
                                }),
                        ])->columnSpan(3)->live()->label('Lista')->collapsible()->afterStateUpdated(function ($get,  $set) {
                            $itemsColumn = array_column($get('SaleProducts'), 'total_product');
                            $sumaItemsColumn = array_sum($itemsColumn);
                            $set('total', $sumaItemsColumn);
                        }),

                        Forms\Components\Placeholder::make('total')->content(function (callable $get, callable $set, $state) {

                            $itemsColumn = array_column($get('SaleProducts'), 'total_product');
                            $sumaItemsColumn = array_sum($itemsColumn);
                            $set('../../total', str_replace(',', '.', $sumaItemsColumn));
                            $set('total', str_replace(',', '.', $sumaItemsColumn));
                            $set('../../sale_price', $get('total_product'));

                            return number_format($sumaItemsColumn, 2, '.', '');
                        })->dehydrated()
                    ])->columns(4)
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        $paymentMethodMapping = [
            'pagomovil' => 'Pago Móvil',
            'efectivo' => 'Efectivo',
            'tarjeta' => 'Tarjeta'
        ];

        return $table
            ->columns([

                Tables\Columns\TextColumn::make('order_number')->label('Número de factura'),
                Tables\Columns\TextColumn::make('payment_method')->label('Método de pago')
                    ->sortable()->formatStateUsing(fn ($state) => $paymentMethodMapping[$state] ?? $state),
                Tables\Columns\TextColumn::make('total')->label('Monto')
                    ->numeric()
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha de Venta')->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ActionGroup::make([
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
