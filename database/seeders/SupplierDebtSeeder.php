<?php

namespace Database\Seeders;

use App\Models\SupplierDebt;
use Database\Seeders\Concerns\ParsesExcelData;
use Illuminate\Database\Seeder;

class SupplierDebtSeeder extends Seeder
{
    use ParsesExcelData;

    /**
     * Import supplier debts (deudas_proveedores.xlsx).
     * Columns: #, Proveedor, Total Deuda, Total Pagado, Saldo Pendiente, Estado, Fecha Registro
     */
    public function run(): void
    {
        if (SupplierDebt::count() > 0) {
            $this->command?->warn('SupplierDebts ya tiene datos, se omite la importación.');
            return;
        }

        $imported = 0;

        foreach ($this->loadRows('deudas_proveedores.xlsx') as $row) {
            $registeredAt = $this->parseDate($row['G'], 'd/m/Y') ?? now();

            $debt = new SupplierDebt();

            SupplierDebt::withoutTimestamps(function () use ($debt, $row, $registeredAt) {
                $debt->forceFill([
                    'name' => trim((string) $row['B']),
                    'debt_amount' => $this->money($row['C']),
                    'paid_amount' => $this->money($row['D']),
                    'status' => strtolower(trim((string) $row['F'])),
                    'created_at' => $registeredAt,
                    'updated_at' => $registeredAt,
                ])->save();
            });

            $imported++;
        }

        $this->command?->info("Deudas de proveedores importadas: {$imported}");
    }
}
