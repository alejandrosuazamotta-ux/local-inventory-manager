<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Sale;
use Database\Seeders\Concerns\ParsesExcelData;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    use ParsesExcelData;

    /**
     * Import paid invoices (facturas.xlsx, invoice_type=paid) and credit
     * invoices (creditos.xlsx, invoice_type=credit). Individual line items
     * are not present in either export (only invoice totals), so sale_details
     * are intentionally left empty — the current stock figures already
     * reflect all historical movement and would be double-counted otherwise.
     */
    public function run(): void
    {
        if (Sale::count() > 0) {
            $this->command?->warn('Sales ya tiene datos, se omite la importación.');
            return;
        }

        $byDocumentNumber = Customer::all()->keyBy('document_number');
        $byName = Customer::all()->keyBy(fn (Customer $c) => strtolower(trim($c->name)));

        $paidImported = $this->importPaidInvoices($byDocumentNumber);
        $creditImported = $this->importCreditInvoices($byName);

        $this->command?->info("Facturas de contado importadas: {$paidImported}");
        $this->command?->info("Créditos importados: {$creditImported}");
    }

    /**
     * facturas.xlsx columns: NO. FACTURA, CLIENTE, DOCUMENTO/NIT, TELÉFONO, FECHA, TOTAL
     */
    private function importPaidInvoices($byDocumentNumber): int
    {
        $count = 0;

        foreach ($this->loadRows('facturas.xlsx') as $row) {
            $documentNumber = $this->docNumber($row['A']);
            $customerDoc = $this->text($row['C']);
            $customer = $customerDoc ? $byDocumentNumber->get($customerDoc) : null;

            if (! $customer) {
                $this->command?->warn("Factura {$row['A']}: cliente no encontrado (doc {$customerDoc}), se omite.");
                continue;
            }

            $issuedAt = $this->parseDate($row['E'], 'd/m/Y') ?? now();
            $total = $this->money($row['F']);

            $sale = new Sale();

            Sale::withoutTimestamps(function () use ($sale, $customer, $documentNumber, $issuedAt, $total) {
                $sale->forceFill([
                    'customer_id' => $customer->id,
                    'user_id' => null,
                    'total' => $total,
                    'status' => 'paid',
                    'payment_method' => 'cash',
                    'invoice_type' => 'paid',
                    'document_number' => $documentNumber,
                    'due_date' => null,
                    'initial_payment' => $total,
                    'pending_balance' => 0,
                    'discount' => 0,
                    'created_at' => $issuedAt,
                    'updated_at' => $issuedAt,
                ])->save();
            });

            $count++;
        }

        return $count;
    }

    /**
     * creditos.xlsx columns: NO. FACTURA, CLIENTE, TELÉFONO, FECHA EMISIÓN,
     * FECHA VENCIMIENTO, ESTADO, TOTAL, ABONOS, SALDO PENDIENTE
     */
    private function importCreditInvoices($byName): int
    {
        $count = 0;

        foreach ($this->loadRows('creditos.xlsx') as $row) {
            $documentNumber = $this->docNumber($row['A']);
            $customerName = strtolower(trim((string) $row['B']));
            $customer = $byName->get($customerName);

            if (! $customer) {
                $this->command?->warn("Crédito {$row['A']}: cliente '{$row['B']}' no encontrado, se omite.");
                continue;
            }

            $issuedAt = $this->parseDate($row['D'], 'd/m/Y') ?? now();
            $dueDate = $this->parseDate($row['E'], 'd/m/Y');
            $total = $this->money($row['G']);
            $abonos = $this->money($row['H']);
            $pendingBalance = max(0, $total - $abonos);
            $status = strtolower(trim((string) $row['F'])) === 'pagado' ? 'paid' : 'pending';

            $sale = new Sale();

            Sale::withoutTimestamps(function () use ($sale, $customer, $documentNumber, $issuedAt, $dueDate, $total, $pendingBalance, $status) {
                $sale->forceFill([
                    'customer_id' => $customer->id,
                    'user_id' => null,
                    'total' => $total,
                    'status' => $status,
                    'payment_method' => 'cash',
                    'invoice_type' => 'credit',
                    'document_number' => $documentNumber,
                    'due_date' => $dueDate?->format('Y-m-d'),
                    'initial_payment' => 0,
                    'pending_balance' => $pendingBalance,
                    'discount' => 0,
                    'created_at' => $issuedAt,
                    'updated_at' => $issuedAt,
                ])->save();
            });

            $count++;
        }

        return $count;
    }
}
