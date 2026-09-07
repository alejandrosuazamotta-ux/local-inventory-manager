<?php

namespace Database\Seeders;

use App\Models\CarteraAntigua;
use Database\Seeders\Concerns\ParsesExcelData;
use Illuminate\Database\Seeder;

class CarteraAntiguaSeeder extends Seeder
{
    use ParsesExcelData;

    /**
     * Import legacy/old portfolio debts (cartera_antigua.xlsx).
     * Columns: #, Cliente, Deuda Total, Monto Pagado, Saldo Pendiente, Estado, Fecha Registro, Observaciones
     */
    public function run(): void
    {
        if (CarteraAntigua::count() > 0) {
            $this->command?->warn('CarteraAntigua ya tiene datos, se omite la importación.');
            return;
        }

        $imported = 0;

        foreach ($this->loadRows('cartera_antigua.xlsx') as $row) {
            $registeredAt = $this->parseDate($row['G'], 'd/m/Y') ?? now();

            $record = new CarteraAntigua();

            CarteraAntigua::withoutTimestamps(function () use ($record, $row, $registeredAt) {
                $record->forceFill([
                    'nombre_cliente' => trim((string) $row['B']),
                    'deuda_total' => $this->money($row['C']),
                    'monto_pagado' => $this->money($row['D']),
                    'estado' => strtolower(trim((string) $row['F'])),
                    'fecha_registro' => $registeredAt->format('Y-m-d'),
                    'observaciones' => $this->text($row['H']),
                    'created_at' => $registeredAt,
                    'updated_at' => $registeredAt,
                ])->save();
            });

            $imported++;
        }

        $this->command?->info("Cartera antigua importada: {$imported}");
    }
}
