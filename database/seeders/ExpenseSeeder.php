<?php

namespace Database\Seeders;

use App\Models\Expense;
use Database\Seeders\Concerns\ParsesExcelData;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    use ParsesExcelData;

    /**
     * Import the expense history (historial_gastos.xlsx).
     * Columns: NO. GASTO, DESCRIPCIÓN, FECHA GASTO, FECHA REGISTRO, MONTO
     * (this export predates the "type" column added to expenses, so all
     * imported rows default to type=personal)
     */
    public function run(): void
    {
        if (Expense::count() > 0) {
            $this->command?->warn('Expenses ya tiene datos, se omite la importación.');
            return;
        }

        $imported = 0;

        foreach ($this->loadRows('historial_gastos.xlsx') as $row) {
            $expenseDate = $this->parseDate($row['C'], 'd/m/Y') ?? now();
            $registeredAt = $this->parseDate($row['D'], 'd/m/Y H:i') ?? $expenseDate;

            $expense = new Expense();

            Expense::withoutTimestamps(function () use ($expense, $row, $expenseDate, $registeredAt) {
                $expense->forceFill([
                    'description' => trim((string) $row['B']),
                    'amount' => $this->money($row['E']),
                    'date' => $expenseDate->format('Y-m-d'),
                    'type' => 'personal',
                    'created_at' => $registeredAt,
                    'updated_at' => $registeredAt,
                ])->save();
            });

            $imported++;
        }

        $this->command?->info("Gastos importados: {$imported}");
    }
}
