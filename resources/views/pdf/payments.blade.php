@extends('pdf.layout')
@section('title', 'Historial de Abonos')
@section('content')
<table class="data-table">
    <thead>
        <tr>
            <th>No. Abono</th><th>Documento</th><th>Cliente</th><th>Fecha</th><th>Método</th><th class="text-right">Monto</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr>
            <td>ABN-{{ $item->id }}</td>
            <td>{{ $item->sale->invoice_type === 'credit' ? 'CRD-' : 'FAC-' }}{{ $item->sale->document_number ?? 'N/A' }}</td>
            <td>{{ $item->customer->name ?? 'Consumidor Final' }}</td>
            <td>{{ $item->date ? $item->date->format('d/m/Y') : $item->created_at->format('d/m/Y') }}</td>
            <td>{{ $item->payment_method == 'cash' ? 'Efectivo' : ucfirst($item->payment_method) }}</td>
            <td class="text-right fw-bold text-success">${{ number_format($item->amount, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection