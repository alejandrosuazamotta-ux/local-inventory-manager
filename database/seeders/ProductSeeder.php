<?php

namespace Database\Seeders;

use App\Models\Product;
use Database\Seeders\Concerns\ParsesExcelData;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    use ParsesExcelData;

    /**
     * Import the product inventory (inventario_productos.xlsx).
     * Columns: Nº, Nombre del Producto, Cantidad Actual, Costo de Compra ($),
     *          Precio de Venta ($), Utilidad Neta ($), Margen de Ganancia (%), Fecha de Registro
     */
    public function run(): void
    {
        if (Product::count() > 0) {
            $this->command?->warn('Products ya tiene datos, se omite la importación.');
            return;
        }

        $imported = 0;

        foreach ($this->loadRows('inventario_productos.xlsx') as $row) {
            $name = trim((string) $row['B']);

            if ($name === '') {
                continue;
            }

            $registeredAt = $this->parseDate($row['H'], 'Y-m-d H:i:s') ?? now();

            $product = new Product();

            Product::withoutTimestamps(function () use ($product, $row, $registeredAt) {
                $product->forceFill([
                    'name' => trim((string) $row['B']),
                    'barcode' => null,
                    'price' => $this->money($row['E']),
                    'cost' => $this->money($row['D']),
                    'stock' => $row['C'] !== null ? (int) $row['C'] : 0,
                    'min_stock' => 5,
                    'status' => 'active',
                    'created_at' => $registeredAt,
                    'updated_at' => $registeredAt,
                ])->save();
            });

            $imported++;
        }

        $this->command?->info("Productos importados: {$imported}");
    }
}
