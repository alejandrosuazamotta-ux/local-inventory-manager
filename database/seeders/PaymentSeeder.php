<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Sale;
use Database\Seeders\Concerns\ParsesExcelData;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    use ParsesExcelData;

    /**
     * Import the payment history (historial_abonos.xlsx), linking each abono
     * back to its credit sale via the "CRE-N" document number.
     * Columns: NO. ABONO, DOCUMENTO ORIGEN, CLIENTE, FECHA, MÉTODO PAGO, MONTO
     */
    public function run(): void
    {
        if (Payment::count() > 0) {
            $this->command?->warn('Payments ya tiene datos, se omite la importación.');
            return;
        }

        $creditsByDocNumber = Sale::where('invoice_type', 'credit')->get()->keyBy('document_number');
        $imported = 0;

        foreach ($this->loadRows('historial_abonos.xlsx') as $row) {
            $documentNumber = $this->docNumber($row['B']);
            $sale = $creditsByDocNumber->get($documentNumber);

            if (! $sale) {
                $this->command?->warn("Abono {$row['A']}: crédito {$row['B']} no encontrado, se omite.");
                continue;
            }

            $paidAt = $this->parseDate($row['D'], 'd/m/Y') ?? now();
            $method = strtolower(trim((string) $row['E'])) === 'efectivo' ? 'cash' : strtolower(trim((string) $row['E']));

            $payment = new Payment();

            Payment::withoutTimestamps(function () use ($payment, $sale, $paidAt, $method, $row) {
                $payment->forceFill([
                    'sale_id' => $sale->id,
                    'customer_id' => $sale->customer_id,
                    'amount' => $this->money($row['F']),
                    'payment_method' => $method,
                    'date' => $paidAt->format('Y-m-d'),
                    'created_at' => $paidAt,
                    'updated_at' => $paidAt,
                ])->save();
            });

            $imported++;
        }

        $this->command?->info("Abonos importados: {$imported}");
    }
}
