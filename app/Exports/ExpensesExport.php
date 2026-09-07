<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ExpensesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function query()
    {
        return Expense::query()->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'NO. GASTO',
            'TIPO DE GASTO',
            'DESCRIPCIÓN',
            'FECHA GASTO',
            'FECHA REGISTRO',
            'MONTO'
        ];
    }

    public function map($expense): array
    {
        return [
            'GST-' . $expense->id,
            ($expense->type ?? 'personal') === 'merchandise' ? 'Gasto Mercancía' : 'Gasto Personal',
            $expense->description,
            $expense->date ? $expense->date->format('d/m/Y') : '',
            $expense->created_at ? $expense->created_at->format('d/m/Y H:i') : '',
            $expense->amount,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => '#,##0',
        ];
    }
}
