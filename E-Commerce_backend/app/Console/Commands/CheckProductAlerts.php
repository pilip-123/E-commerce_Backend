<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\ProductAlertService;
use Illuminate\Console\Command;

class CheckProductAlerts extends Command
{
    protected $signature = 'products:check-alerts';
    protected $description = 'Check for low stock and out-of-stock products and alert admin/manager roles';

    public function handle(): void
    {
        $products = Product::where('stock', '<=', 3)->get();

        foreach ($products as $product) {
            app(ProductAlertService::class)->checkLowStock($product);
            app(ProductAlertService::class)->checkOutOfStock($product);
        }
    }
}
