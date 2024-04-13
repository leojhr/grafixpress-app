<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use App\Models\Inventory;
use App\Models\SaleProduct;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;
    protected static bool $canCreateAnother = false;

    protected function afterCreate(): void
    {
        $sale = $this->record;
        $products = SaleProduct::where('sale_id', $sale->id)->get();

        foreach ($products as $product) {
            $inventoryProduct = Inventory::where('id', $product->product_id)->first();

            if ($inventoryProduct->is_service) continue;

            if ($inventoryProduct->quantity > $product->quantity) {
                $inventoryProduct->quantity -= $product->quantity;
                $inventoryProduct->save();
            }
        }
    }
}
