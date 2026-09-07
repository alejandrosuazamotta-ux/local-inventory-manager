<?php

namespace Database\Seeders;

use App\Models\Customer;
use Database\Seeders\Concerns\ParsesExcelData;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    use ParsesExcelData;

    /**
     * Import the customer directory (clientes_directorio.xlsx).
     * Columns: #, Cliente, Celular, Cédula, Correo, Dirección, Fecha de Registro
     */
    public function run(): void
    {
        $imported = 0;

        foreach ($this->loadRows('clientes_directorio.xlsx') as $row) {
            $documentNumber = $this->text($row['D']);

            if (! $documentNumber) {
                continue;
            }

            $registeredAt = $this->parseDate($row['G'], 'd/m/Y H:i') ?? now();

            $customer = Customer::firstOrNew(['document_number' => $documentNumber]);

            Customer::withoutTimestamps(function () use ($customer, $row, $registeredAt) {
                $customer->forceFill([
                    'document_type' => 'CC',
                    'name' => trim((string) $row['B']),
                    'phone' => $this->text($row['C']),
                    'email' => $this->text($row['E']),
                    'address' => $this->text($row['F']),
                    'created_at' => $customer->exists ? $customer->created_at : $registeredAt,
                    'updated_at' => $registeredAt,
                ])->save();
            });

            $imported++;
        }

        $this->command?->info("Clientes importados: {$imported}");
    }
}
